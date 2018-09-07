<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

switch($_GET['tab']) {
	case 'inspections':
		$title = 'Inspections';
		$include_file = 'dashboard_inspections.php';
		break;
	case 'assign_equipment':
		$title = 'Assigned Equipment';
		$include_file = 'dashboard_assign_equipment.php';
		break;
	case 'work_orders':
		$title = 'Work Orders';
		$include_file = 'dashboard_work_orders.php';
		break;
	case 'expenses':
		$title = 'Expenses';
		$include_file = 'dashboard_expenses.php';
		break;
	case 'balance':
		$title = 'Balance Sheets';
		$include_file = 'dashboard_balance.php';
		break;
	case 'service_schedules':
		$title = 'Service Schedules';
		$include_file = 'dashboard_service_schedules.php';
		break;
	case 'service_request':
		$title = 'Service Requests';
		$include_file = 'dashboard_service_request.php';
		break;
	case 'service_record':
		$title = 'Service Records';
		$include_file = 'dashboard_service_record.php';
		break;
	case 'equipment_checklist':
		$title = 'Checklist';
		$include_file = 'dashboard_equipment_checklist.php';
		break;
	default:
		$_GET['tab'] = 'equipment';
		$_GET['status'] = $_GET['status'] == 'Inactive' ? 'Inactive' : 'Active';
		$title = $_GET['status'].' Equipment';
		$include_file = 'dashboard_equipment.php';
		break;
}
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
// Add Default Configurations for the default Tabs if nothing is configured
$inspection_list = filter_var(implode('#*#', ["Oil","Coolant - Rad","Coolant Overflow","Hydraulic Oil","Hydraulic Oil - Leaks","Transmission Oil","Air Filters","Belts","Track SAG","Brake Emergency","Planetaries","Brake Pedal","Hydraulic Brake Fluid","Parking Brake","Defroster & Heaters","Emergency Equipment","Engine","Exhaust System","Fire Extinguisher","Fuel System","Generator/Alternator","Horn","Lights & Reflectors","Head - Stop Lights","Tail - Dash Lights","Blade","Bucket","Body Damage","Doors","Mirrors (Adjustment & Condition)","Oil Pressure","Radiator","Driver's Seat Belt & Seat Security","Cutting Edges","Ripper Teeth","Towing & Coupling Devices","Windshield & Windows","Windshield Washer & Wipers"]),FILTER_SANITIZE_STRING);
mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'equipment_tabs', 'Truck,Trailer' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_tabs') num WHERE num.rows=0");
mysqli_query($dbc, "INSERT INTO `field_config_equipment` (`tab`, `equipment_dashboard`, `inspection_list`) SELECT 'Truck', 'Make,Model,Unit #,Service History Link,Registration Renewal date,Insurance Renewal Date,Service History Link,Status', '$inspection_list' FROM (SELECT COUNT(*) rows FROM `field_config_equipment` WHERE `equipment_dashboard` IS NOT NULL AND `tab`='Truck') num WHERE num.rows=0");
mysqli_query($dbc, "INSERT INTO `field_config_equipment` (`tab`, `accordion`, `order`, `equipment`) SELECT `tab`, `accordion`, `sort`, `field_names` FROM (SELECT 'Truck' `tab`, 'Equipment Information' `accordion`, '1' `sort`, 'Category,Make,Model,Model Year,Color,Unit #,VIN #' `field_names` UNION SELECT 'Truck', 'Registration', '2', 'Licence Plate,Registration Card,Registration Renewal date,Registration Reminder' UNION SELECT 'Truck', 'Insurance', '3', 'Insurance,Insurance Contact,Insurance Phone,Insurance Card,Insurance Renewal Date,Insurance Reminder' UNION SELECT 'Truck', 'Status', '4', 'Status') defaults INNER JOIN (SELECT COUNT(*) rows FROM `field_config_equipment` WHERE `equipment` IS NOT NULL AND `tab`='Truck') num WHERE num.rows=0");
mysqli_query($dbc, "INSERT INTO `field_config_equipment` (`tab`, `equipment_dashboard`, `inspection_list`) SELECT 'Trailer', 'Make,Model,Unit #,Registration Renewal date,Insurance Renewal Date,Service History Link,Status', '$inspection_list' FROM (SELECT COUNT(*) rows FROM `field_config_equipment` WHERE `equipment_dashboard` IS NOT NULL AND `tab`='Trailer') num WHERE num.rows=0");
mysqli_query($dbc, "INSERT INTO `field_config_equipment` (`tab`, `accordion`, `order`, `equipment`) SELECT `tab`, `accordion`, `sort`, `field_names` FROM (SELECT 'Trailer' `tab`, 'Equipment Information' `accordion`, '1' `sort`, 'Category,Make,Model,Model Year,Color,Unit #,VIN #' `field_names` UNION SELECT 'Trailer', 'Registration', '2', 'Licence Plate,Registration Card,Registration Renewal date,Registration Reminder' UNION SELECT 'Trailer', 'Insurance', '3', 'Insurance,Insurance Contact,Insurance Phone,Insurance Card,Insurance Renewal Date,Insurance Reminder' UNION SELECT 'Trailer', 'Status', '4', 'Status') defaults INNER JOIN (SELECT COUNT(*) rows FROM `field_config_equipment` WHERE `equipment` IS NOT NULL AND `tab`='Trailer') num WHERE num.rows=0");
?>

<script type="text/javascript">
$(document).ready(function() {
	$('#mobile_tabs .panel-heading').click(loadPanel);	
});
function loadPanel() {
	if(!$(this).hasClass('higher_level_heading')) {
		var panel = $(this).closest('.panel').find('.panel-body');
		panel.html('Loading...');
		$.ajax({
			url: panel.data('file-name'),
			method: 'GET',
			response: 'html',
			success: function(response) {
				panel.html(response);
				$('.pagination_links a').click(pagination_load);
			}
		});
	}
}
function pagination_load() {
	var target = $(this).closest('.panel').find('.panel-body');
	$.ajax({
		url: this.href,
		method: 'POST',
		response: 'html',
		success: function(response) {
			target.html(response);
			$('.pagination_links a').click(pagination_load);
		}
	});
	return false;
}
</script>

<div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" id="mobile_tabs">
	<?php if(in_array('Equipment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'equipment') === TRUE) { ?>
		<?php if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_active">
							Active Equipment<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_equipment_active" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="dashboard_equipment.php?status=Active&category=Top&mobile_view=1">
						Loading...
					</div>
				</div>
			</div>
	    <?php } else { ?>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load higher_level_heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_active">
							Active Equipment<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_equipment_active" class="panel-collapse collapse">
					<div class="panel-body" style="padding: 0; margin: -1px;" id="mobile_tabs_equipment_active">

						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title double-gap-left">
									<a data-toggle="collapse" data-parent="#mobile_tabs_equipment_active" href="#collapse_equipment_active_top">
										Last 25 Added<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_equipment_active_top" class="panel-collapse collapse">
								<div class="panel-body" data-file-name="dashboard_equipment.php?status=Active&category=Top&mobile_view=1">
									Loading...
								</div>
							</div>
						</div>

						<?php $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
						foreach($each_tab as $cat_tab) { ?>
							<div class="panel panel-default">
								<div class="panel-heading mobile_load">
									<h4 class="panel-title double-gap-left">
										<a data-toggle="collapse" data-parent="#mobile_tabs_equipment_active" href="#collapse_equipment_active_<?= config_safe_str($cat_tab) ?>">
											<?= $cat_tab ?><span class="glyphicon glyphicon-plus"></span>
										</a>
									</h4>
								</div>

								<div id="collapse_equipment_active_<?= config_safe_str($cat_tab) ?>" class="panel-collapse collapse">
									<div class="panel-body" data-file-name="dashboard_equipment.php?status=Active&category=<?= $cat_tab ?>&mobile_view=1">
										Loading...
									</div>
								</div>
							</div>
						<?php } ?>

					</div>
				</div>
			</div>
	    <?php } ?>
	<?php } ?>
	<?php if(in_array('Equipment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'equipment') === TRUE) { ?>
		<?php if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_inactive">
							Inactive Equipment<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_equipment_inactive" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="dashboard_equipment.php?status=Inactive&category=Top&mobile_view=1">
						Loading...
					</div>
				</div>
			</div>
	    <?php } else { ?>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load higher_level_heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_inactive">
							Inactive Equipment<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_equipment_inactive" class="panel-collapse collapse">
					<div class="panel-body" style="padding: 0; margin: -1px;" id="mobile_tabs_equipment_inactive">

						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title double-gap-left">
									<a data-toggle="collapse" data-parent="#mobile_tabs_equipment_inactive" href="#collapse_equipment_inactive_top">
										Last 25 Added<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_equipment_inactive_top" class="panel-collapse collapse">
								<div class="panel-body" data-file-name="dashboard_equipment.php?status=Inactive&category=Top&mobile_view=1">
									Loading...
								</div>
							</div>
						</div>

						<?php $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
						foreach($each_tab as $cat_tab) { ?>
							<div class="panel panel-default">
								<div class="panel-heading mobile_load">
									<h4 class="panel-title double-gap-left">
										<a data-toggle="collapse" data-parent="#mobile_tabs_equipment_inactive" href="#collapse_equipment_inactive_<?= config_safe_str($cat_tab) ?>">
											<?= $cat_tab ?><span class="glyphicon glyphicon-plus"></span>
										</a>
									</h4>
								</div>

								<div id="collapse_equipment_inactive_<?= config_safe_str($cat_tab) ?>" class="panel-collapse collapse">
									<div class="panel-body" data-file-name="dashboard_equipment.php?status=Inactive&category=<?= $cat_tab ?>&mobile_view=1">
										Loading...
									</div>
								</div>
							</div>
						<?php } ?>

					</div>
				</div>
			</div>
	    <?php } ?>
	<?php } ?>
	<?php if ( in_array('Inspection',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'inspection') === TRUE ) { ?>
		<div class="panel panel-default">
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_inspection">
						Inspections<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_equipment_inspection" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="dashboard_inspections.php?mobile_view=1">
					Loading...
				</div>
			</div>
		</div>
	<?php } ?>
	<?php if(in_array('Equipment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'equipment') === TRUE) { ?>
		<?php if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_assign">
							Assigned Equipment<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_equipment_assign" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="dashboard_assign_equipment.php?category=Top&mobile_view=1">
						Loading...
					</div>
				</div>
			</div>
	    <?php } else { ?>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load higher_level_heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_assign">
							Assigned Equipment<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_equipment_assign" class="panel-collapse collapse">
					<div class="panel-body" style="padding: 0; margin: -1px;" id="mobile_tabs_equipment_assign">

						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title double-gap-left">
									<a data-toggle="collapse" data-parent="#mobile_tabs_equipment_assign" href="#collapse_equipment_assign_top">
										Last 25 Added<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_equipment_assign_top" class="panel-collapse collapse">
								<div class="panel-body" data-file-name="dashboard_assign_equipment.php?category=Top&mobile_view=1">
									Loading...
								</div>
							</div>
						</div>

						<?php $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
						foreach($each_tab as $cat_tab) { ?>
							<div class="panel panel-default">
								<div class="panel-heading mobile_load">
									<h4 class="panel-title double-gap-left">
										<a data-toggle="collapse" data-parent="#mobile_tabs_equipment_assign" href="#collapse_equipment_assign_<?= config_safe_str($cat_tab) ?>">
											<?= $cat_tab ?><span class="glyphicon glyphicon-plus"></span>
										</a>
									</h4>
								</div>

								<div id="collapse_equipment_assign_<?= config_safe_str($cat_tab) ?>" class="panel-collapse collapse">
									<div class="panel-body" data-file-name="dashboard_assign_equipment.php?category=<?= $cat_tab ?>&mobile_view=1">
										Loading...
									</div>
								</div>
							</div>
						<?php } ?>

					</div>
				</div>
			</div>
	    <?php } ?>
	<?php } ?>
	<?php if ( in_array('Work Order',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'work_orders') === TRUE ) { ?>
		<div class="panel panel-default">
			<div class="panel-heading mobile_load higher_level_heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_work_orders">
						Work Orders<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_equipment_work_orders" class="panel-collapse collapse">
				<div class="panel-body" style="padding: 0; margin: -1px;" id="mobile_tabs_equipment_work_orders">
					<?php $each_tab = ['Pending','Doing','Done'];
					foreach($each_tab as $cat_tab) { ?>
						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title double-gap-left">
									<a data-toggle="collapse" data-parent="#mobile_tabs_equipment_work_orders" href="#collapse_equipment_work_orders_<?= config_safe_str($cat_tab) ?>">
										<?= $cat_tab ?><span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_equipment_work_orders_<?= config_safe_str($cat_tab) ?>" class="panel-collapse collapse">
								<div class="panel-body" data-file-name="dashboard_work_orders.php?subtab=<?= $cat_tab ?>&mobile_view=1">
									Loading...
								</div>
							</div>
						</div>
					<?php } ?>

				</div>
			</div>
		</div>
    <?php } ?>
	<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
		<div class="panel panel-default">
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_expenses">
						Expenses<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_equipment_expenses" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="dashboard_expenses.php?mobile_view=1">
					Loading...
				</div>
			</div>
		</div>
	<?php } ?>
	<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
		<div class="panel panel-default">
			<div class="panel-heading mobile_load higher_level_heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_balance">
						Balance Sheets<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_equipment_balance" class="panel-collapse collapse">
				<div class="panel-body" style="padding: 0; margin: -1px;" id="mobile_tabs_equipment_balance">

					<div class="panel panel-default">
						<div class="panel-heading mobile_load">
							<h4 class="panel-title double-gap-left">
								<a data-toggle="collapse" data-parent="#mobile_tabs_equipment_balance" href="#collapse_equipment_balance_active">
									Active Equipment<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_equipment_balance_active" class="panel-collapse collapse">
							<div class="panel-body" data-file-name="dashboard_balance.php?status=Active&mobile_view=1">
								Loading...
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading mobile_load">
							<h4 class="panel-title double-gap-left">
								<a data-toggle="collapse" data-parent="#mobile_tabs_equipment_balance" href="#collapse_equipment_balance_inactive">
									Inactive Equipment<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_equipment_balance_inactive" class="panel-collapse collapse">
							<div class="panel-body" data-file-name="dashboard_balance.php?status=Inactive&mobile_view=1">
								Loading...
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
    <?php } ?>
	<?php if ( in_array('Schedules',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'schedules') === TRUE ) { ?>
		<div class="panel panel-default">
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_schedules">
						Service Schedules<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_equipment_schedules" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="dashboard_service_schedules.php?mobile_view=1">
					Loading...
				</div>
			</div>
		</div>
	<?php } ?>
	<?php if(in_array('Requests',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'requests') === TRUE) { ?>
		<?php if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_service_requests">
							Service Requests<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_equipment_service_requests" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="dashboard_service_request.php?category=Top&mobile_view=1">
						Loading...
					</div>
				</div>
			</div>
	    <?php } else { ?>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load higher_level_heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_service_requests">
							Service Requests<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_equipment_service_requests" class="panel-collapse collapse">
					<div class="panel-body" style="padding: 0; margin: -1px;" id="mobile_tabs_equipment_service_requests">

						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title double-gap-left">
									<a data-toggle="collapse" data-parent="#mobile_tabs_equipment_service_requests" href="#collapse_equipment_service_requests_top">
										Last 25 Added<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_equipment_service_requests_top" class="panel-collapse collapse">
								<div class="panel-body" data-file-name="dashboard_service_request.php?category=Top&mobile_view=1">
									Loading...
								</div>
							</div>
						</div>

						<?php $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
						foreach($each_tab as $cat_tab) { ?>
							<div class="panel panel-default">
								<div class="panel-heading mobile_load">
									<h4 class="panel-title double-gap-left">
										<a data-toggle="collapse" data-parent="#mobile_tabs_equipment_service_requests" href="#collapse_equipment_service_requests_<?= config_safe_str($cat_tab) ?>">
											<?= $cat_tab ?><span class="glyphicon glyphicon-plus"></span>
										</a>
									</h4>
								</div>

								<div id="collapse_equipment_service_requests_<?= config_safe_str($cat_tab) ?>" class="panel-collapse collapse">
									<div class="panel-body" data-file-name="dashboard_service_request.php?category=<?= $cat_tab ?>&mobile_view=1">
										Loading...
									</div>
								</div>
							</div>
						<?php } ?>

					</div>
				</div>
			</div>
	    <?php } ?>
	<?php } ?>
	<?php if(in_array('Records',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'records') === TRUE) { ?>
		<?php if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_service_records">
							Service Records<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_equipment_service_records" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="dashboard_service_record.php?category=Top&mobile_view=1">
						Loading...
					</div>
				</div>
			</div>
	    <?php } else { ?>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load higher_level_heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_service_records">
							Service Records<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_equipment_service_records" class="panel-collapse collapse">
					<div class="panel-body" style="padding: 0; margin: -1px;" id="mobile_tabs_equipment_service_records">

						<div class="panel panel-default">
							<div class="panel-heading mobile_load">
								<h4 class="panel-title double-gap-left">
									<a data-toggle="collapse" data-parent="#mobile_tabs_equipment_service_records" href="#collapse_equipment_service_records_top">
										Last 25 Added<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_equipment_service_records_top" class="panel-collapse collapse">
								<div class="panel-body" data-file-name="dashboard_service_record.php?category=Top&mobile_view=1">
									Loading...
								</div>
							</div>
						</div>

						<?php $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
						foreach($each_tab as $cat_tab) { ?>
							<div class="panel panel-default">
								<div class="panel-heading mobile_load">
									<h4 class="panel-title double-gap-left">
										<a data-toggle="collapse" data-parent="#mobile_tabs_equipment_service_records" href="#collapse_equipment_service_records_<?= config_safe_str($cat_tab) ?>">
											<?= $cat_tab ?><span class="glyphicon glyphicon-plus"></span>
										</a>
									</h4>
								</div>

								<div id="collapse_equipment_service_records_<?= config_safe_str($cat_tab) ?>" class="panel-collapse collapse">
									<div class="panel-body" data-file-name="dashboard_service_record.php?category=<?= $cat_tab ?>&mobile_view=1">
										Loading...
									</div>
								</div>
							</div>
						<?php } ?>

					</div>
				</div>
			</div>
	    <?php } ?>
	<?php } ?>
	<?php if ( in_array('Checklists',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'checklist') === TRUE ) { ?>
		<div class="panel panel-default">
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equipment_checklist">
						Checklists<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_equipment_checklist" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="dashboard_equipment_checklist.php?mobile_view=1">
					Loading...
				</div>
			</div>
		</div>
	<?php } ?>
</div>

<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
	<ul>
		<form action="?" method="GET">
			<li class="standard-sidebar-searchbox">
				<input type="text" name="search_equipment" class="form-control" placeholder="Search Equipment" value="<?= $_GET['search_equipment'] ?>">
				<input type="hidden" name="category" value="<?= $_GET['category'] ?>">
	            <input type="submit" value="Search" class="btn brand-btn" name="search_equipment_submit" style="display:none;" />
	        </li>
	    </form>
		<?php if ( in_array('Equipment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'equipment') === TRUE ) { ?>
			<?php if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
			    <a href="?category=Top&status=Active"><li class="<?= $_GET['tab'] == 'equipment' && $_GET['status'] == 'Active' ? 'active blue' : '' ?>">Active Equipment</li></a>
		    <?php } else { ?>
				<li class="sidebar-higher-level"><a class="cursor-hand <?= $_GET['tab'] == 'equipment' && $_GET['status'] == 'Active' ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#sidebar_active">Active Equipment<span class="arrow"></span></a>
					<ul class="<?= $_GET['tab'] == 'equipment' && $_GET['status'] == 'Active' ? 'collapse in' : 'collapse' ?>" id="sidebar_active">
					    <a href="?tab=equipment&status=Active&category=Top"><li class="<?= $_GET['tab'] == 'equipment' && $_GET['status'] == 'Active' && (empty($_GET['category']) || $_GET['category'] == 'Top') ? 'active blue' : '' ?>">Last 25 Added</li></a>
						<?php $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
						foreach($each_tab as $cat_tab) { ?>
						    <a href="?tab=equipment&status=Active&category=<?= $cat_tab ?>"><li class="<?= $_GET['tab'] == 'equipment' && $_GET['status'] == 'Active' && $_GET['category'] == $cat_tab ? 'active blue' : '' ?>"><?= $cat_tab ?></li></a>
						<?php } ?>
					</ul>
				</li>
		    <?php } ?>
		<?php } ?>
		<?php if ( in_array('Equipment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'equipment') === TRUE ) { ?>
			<?php if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
			    <a href="?category=Top&status=Inactive"><li class="<?= $_GET['tab'] == 'equipment' && $_GET['status'] == 'Inactive' ? 'active blue' : '' ?>">Inactive Equipment</li></a>
		    <?php } else { ?>
				<li class="sidebar-higher-level"><a class="cursor-hand <?= $_GET['tab'] == 'equipment' && $_GET['status'] == 'Inactive' ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#sidebar_inactive">Inactive Equipment<span class="arrow"></span></a>
					<ul class="<?= $_GET['tab'] == 'equipment' && $_GET['status'] == 'Inactive' ? 'collapse in' : 'collapse' ?>" id="sidebar_inactive">
					    <a href="?tab=equipment&status=Inactive&category=Top"><li class="<?= $_GET['tab'] == 'equipment' && $_GET['status'] == 'Inactive' && (empty($_GET['category']) || $_GET['category'] == 'Top') ? 'active blue' : '' ?>">Last 25 Added</li></a>
						<?php $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
						foreach($each_tab as $cat_tab) { ?>
						    <a href="?tab=equipment&status=Inactive&category=<?= $cat_tab ?>"><li class="<?= $_GET['tab'] == 'equipment' && $_GET['status'] == 'Inactive' && $_GET['category'] == $cat_tab ? 'active blue' : '' ?>"><?= $cat_tab ?></li></a>
						<?php } ?>
					</ul>
				</li>
		    <?php } ?>
		<?php } ?>
		<?php if ( in_array('Inspection',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'inspection') === TRUE ) { ?>
		    <a href="?tab=inspections"><li class="<?= $_GET['tab'] == 'inspections' ? 'active blue' : '' ?>">Inspections</li></a>
		<?php } ?>
		<?php if ( in_array('Assign',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'assign') === TRUE ) { ?>
			<?php if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
			    <a href="?tab=assign_equipment"><li class="<?= $_GET['tab'] == 'assign_equipment' ? 'active blue' : '' ?>">Assigned Equipment</li></a>
		    <?php } else { ?>
				<li class="sidebar-higher-level"><a class="cursor-hand <?= $_GET['tab'] == 'assign_equipment' ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#sidebar_assign_equip">Assigned Equipment<span class="arrow"></span></a>
					<ul class="<?= $_GET['tab'] == 'assign_equipment' ? 'collapse in' : 'collapse' ?>" id="sidebar_assign_equip">
					    <a href="?tab=assign_equipment&category=Top"><li class="<?= $_GET['tab'] == 'assign_equipment' && (empty($_GET['category']) || $_GET['category'] == 'Top') ? 'active blue' : '' ?>">Last 25 Added</li></a>
						<?php $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
						foreach($each_tab as $cat_tab) { ?>
						    <a href="?tab=assign_equipment&category=<?= $cat_tab ?>"><li class="<?= $_GET['tab'] == 'assign_equipment' && $_GET['category'] == $cat_tab ? 'active blue' : '' ?>"><?= $cat_tab ?></li></a>
						<?php } ?>
					</ul>
				</li>
		    <?php } ?>
		<?php } ?>
		<?php if ( in_array('Work Order',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'work_orders') === TRUE ) { ?>
			<li class="sidebar-higher-level"><a class="cursor-hand <?= $_GET['tab'] == 'work_orders' ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#sidebar_work_orders">Work Orders<span class="arrow"></span></a>
				<ul class="<?= $_GET['tab'] == 'work_orders' ? 'collapse in' : 'collapse' ?>" id="sidebar_work_orders">
				    <a href="?tab=work_orders&subtab=Pending"><li class="<?= $_GET['tab'] == 'work_orders' && (empty($_GET['subtab']) || $_GET['subtab'] == 'Pending') ? 'active blue' : '' ?>">Pending</li></a>
				    <a href="?tab=work_orders&subtab=Doing"><li class="<?= $_GET['tab'] == 'work_orders' && $_GET['subtab'] == 'Doing' ? 'active blue' : '' ?>">Doing</li></a>
				    <a href="?tab=work_orders&subtab=Done"><li class="<?= $_GET['tab'] == 'work_orders' && $_GET['subtab'] == 'Done' ? 'active blue' : '' ?>">Done</li></a>
				</ul>
			</li>
		<?php } ?>
		<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
		    <a href="?tab=expenses"><li class="<?= $_GET['tab'] == 'expenses' ? 'active blue' : '' ?>">Expenses</li></a>
		<?php } ?>
		<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
			<li class="sidebar-higher-level"><a class="cursor-hand <?= $_GET['tab'] == 'balance' ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#sidebar_balance">Balance Sheets<span class="arrow"></span></a>
				<ul class="<?= $_GET['tab'] == 'balance' ? 'collapse in' : 'collapse' ?>" id="sidebar_balance">
				    <a href="?tab=balance&status=Active"><li class="<?= $_GET['tab'] == 'balance' && (empty($_GET['status']) || $_GET['status'] == 'Active') ? 'active blue' : '' ?>">Active Equipment</li></a>
				    <a href="?tab=balance&status=Inactive"><li class="<?= $_GET['tab'] == 'balance' && $_GET['status'] == 'Inactive' ? 'active blue' : '' ?>">Inactive Equipment</li></a>
				</ul>
			</li>
        <?php } ?>
		<?php if ( in_array('Schedules',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'schedules') === TRUE ) { ?>
		    <a href="?tab=service_schedules"><li class="<?= $_GET['tab'] == 'service_schedules' ? 'active blue' : '' ?>">Service Schedules</li></a>
		<?php } ?>
		<?php if ( in_array('Requests',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'requests') === TRUE ) { ?>
			<?php if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
			    <a href="?tab=service_request"><li class="<?= $_GET['tab'] == 'service_request' ? 'active blue' : '' ?>">Service Requests</li></a>
		    <?php } else { ?>
				<li class="sidebar-higher-level"><a class="cursor-hand <?= $_GET['tab'] == 'service_request' ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#sidebar_service_request">Service Requests<span class="arrow"></span></a>
					<ul class="<?= $_GET['tab'] == 'service_request' ? 'collapse in' : 'collapse' ?>" id="sidebar_service_request">
					    <a href="?tab=service_request&category=Top"><li class="<?= $_GET['tab'] == 'service_request' && (empty($_GET['category']) || $_GET['category'] == 'Top') ? 'active blue' : '' ?>">Last 25 Added</li></a>
						<?php $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
						foreach($each_tab as $cat_tab) { ?>
						    <a href="?tab=service_request&category=<?= $cat_tab ?>"><li class="<?= $_GET['tab'] == 'service_request' && $_GET['category'] == $cat_tab ? 'active blue' : '' ?>"><?= $cat_tab ?></li></a>
						<?php } ?>
					</ul>
				</li>
		    <?php } ?>
		<?php } ?>
		<?php if ( in_array('Records',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'records') === TRUE ) { ?>
			<?php if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
			    <a href="?tab=service_record"><li class="<?= $_GET['tab'] == 'service_record' ? 'active blue' : '' ?>">Service Records</li></a>
		    <?php } else { ?>
				<li class="sidebar-higher-level"><a class="cursor-hand <?= $_GET['tab'] == 'service_record' ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#sidebar_service_record">Service Records<span class="arrow"></span></a>
					<ul class="<?= $_GET['tab'] == 'service_record' ? 'collapse in' : 'collapse' ?>" id="sidebar_service_record">
					    <a href="?tab=service_record&category=Top"><li class="<?= $_GET['tab'] == 'service_record' && (empty($_GET['category']) || $_GET['category'] == 'Top') ? 'active blue' : '' ?>">Last 25 Added</li></a>
						<?php $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
						foreach($each_tab as $cat_tab) { ?>
						    <a href="?tab=service_record&category=<?= $cat_tab ?>"><li class="<?= $_GET['tab'] == 'service_record' && $_GET['category'] == $cat_tab ? 'active blue' : '' ?>"><?= $cat_tab ?></li></a>
						<?php } ?>
					</ul>
				</li>
		    <?php } ?>
		<?php } ?>
		<?php if ( in_array('Checklists',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'checklist') === TRUE ) { ?>
		    <a href="?tab=equipment_checklist"><li class="<?= $_GET['tab'] == 'equipment_checklist' ? 'active blue' : '' ?>">Checklists</li></a>
		<?php } ?>
	</ul>
</div>

<div class="scale-to-fill has-main-screen hide-titles-mob" style="overflow: hidden;">
	<div class="main-screen standard<?= $_GET['tab'] == 'equipment' ? '-dashboard' : '' ?>-body form-horizontal">
		<div class="standard<?= $_GET['tab'] == 'equipment' ? '-dashboard' : '' ?>-body-title">
			<h3><?= $title ?></h3>
		</div>

		<div class="standard<?= $_GET['tab'] == 'equipment' ? '-dashboard' : '' ?>-body-content" style="padding: 1em;">
			<?php include($include_file); ?>
		</div>
	</div>
</div>