<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');
switch($_GET['settings']) {
	case 'field':
		$include_file = 'field_config_field.php';
		$title = 'Fields';
		break;
	case 'mandatory_fields':
		$include_file = 'field_config_mandatory_field.php';
		break;
	case 'dashboard':
		$include_file = 'field_config_dashboard.php';
		$title = 'Dashboard';
		break;
	case 'overview':
		$include_file = 'field_config_overview.php';
		$title = 'Overview';
		break;
	case 'inspection':
		$include_file = 'field_config_inspection.php';
		$title = 'Inspections';
		break;
	case 'expenses':
		$include_file = 'field_config_expenses.php';
		$title = 'Expenses';
		break;
	case 'service_request':
		$include_file = 'field_config_service_request.php';
		$title = 'Service Request';
		break;
	case 'service_record':
		$include_file = 'field_config_service_record.php';
		$title = 'Service Record';
		break;
	case 'equip_assign':
		$include_file = 'field_config_equip_assign.php';
		$title = 'Equipment Assignment';
		break;
	case 'classification':
		$include_file = 'field_config_classification.php';
		$title = 'Classifications';
		break;
	case 'quick_action':
		$include_file = 'field_config_quick_action.php';
		$title = 'Quick Action Icons';
		break;
	default:
		$_GET['settings'] = 'tab';
		$include_file = 'field_config_tab.php';
		$title = 'General';
		break;
}
?>

<script type="text/javascript">
$(document).ready(function() {
	$('#mobile_tabs .panel-heading').click(loadPanel);
});
function loadPanel() {
	var panel = $(this).closest('.panel').find('.panel-body');
	panel.html('Loading...');
	$.ajax({
		url: panel.data('file-name'),
		method: 'GET',
		response: 'html',
		success: function(response) {
			panel.html(response);
		}
	});
}
</script>

<div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" id="mobile_tabs">
	<div class="panel panel-default">
		<div class="panel-heading mobile_load">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_tab">
					General<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_tab" class="panel-collapse collapse">
			<div class="panel-body" data-file-name="field_config_tab.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading mobile_load">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_field">
					Fields<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_field" class="panel-collapse collapse">
			<div class="panel-body" data-file-name="field_config_field.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading mobile_load">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_field">
					Mandatory Fields<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_field" class="panel-collapse collapse">
			<div class="panel-body" data-file-name="field_config_mandatory_field.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading mobile_load">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_dashboard">
					Dashboard<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_dashboard" class="panel-collapse collapse">
			<div class="panel-body" data-file-name="field_config_dashboard.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading mobile_load">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_overview">
					Overview<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_overview" class="panel-collapse collapse">
			<div class="panel-body" data-file-name="field_config_overview.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading mobile_load">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_inspection">
					Inspections<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_inspection" class="panel-collapse collapse">
			<div class="panel-body" data-file-name="field_config_inspection.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading mobile_load">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_expenses">
					Expenses<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_expenses" class="panel-collapse collapse">
			<div class="panel-body" data-file-name="field_config_expenses.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading mobile_load">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_service_request">
					Service Request<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_service_request" class="panel-collapse collapse">
			<div class="panel-body" data-file-name="field_config_service_request.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading mobile_load">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_service_record">
					Service Record<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_service_record" class="panel-collapse collapse">
			<div class="panel-body" data-file-name="field_config_service_record.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading mobile_load">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_equip_assign">
					Equipment Assignment<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_equip_assign" class="panel-collapse collapse">
			<div class="panel-body" data-file-name="field_config_equip_assign.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading mobile_load">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_classification">
					Classifications<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_classification" class="panel-collapse collapse">
			<div class="panel-body" data-file-name="field_config_classification.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading mobile_load">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_quick_action">
					Quick Action Icons<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_quick_action" class="panel-collapse collapse">
			<div class="panel-body" data-file-name="field_config_quick_action.php">
				Loading...
			</div>
		</div>
	</div>
</div>

<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
	<ul>
		<a href="?settings=tab"><li class="<?= $_GET['settings'] == 'tab' ? 'active blue' : '' ?>">General</li></a>
		<a href="?settings=field"><li class="<?= $_GET['settings'] == 'field' ? 'active blue' : '' ?>">Fields</li></a>
		<a href="?settings=mandatory_fields"><li class="<?= $_GET['settings'] == 'mandatory_fields' ? 'active blue' : '' ?>">Mandatory Fields</li></a>
		<a href="?settings=dashboard"><li class="<?= $_GET['settings'] == 'dashboard' ? 'active blue' : '' ?>">Dashboard</li></a>
		<a href="?settings=overview"><li class="<?= $_GET['settings'] == 'overview' ? 'active blue' : '' ?>">Overview</li></a>
		<a href="?settings=inspection"><li class="<?= $_GET['settings'] == 'inspection' ? 'active blue' : '' ?>">Inspections</li></a>
		<a href="?settings=expenses"><li class="<?= $_GET['settings'] == 'expenses' ? 'active blue' : '' ?>">Expenses</li></a>
		<a href="?settings=service_request"><li class="<?= $_GET['settings'] == 'service_request' ? 'active blue' : '' ?>">Service Request</li></a>
		<a href="?settings=service_record"><li class="<?= $_GET['settings'] == 'service_record' ? 'active blue' : '' ?>">Service Record</li></a>
		<a href="?settings=equip_assign"><li class="<?= $_GET['settings'] == 'equip_assign' ? 'active blue' : '' ?>">Equipment Assignment</li></a>
		<a href="?settings=classification"><li class="<?= $_GET['settings'] == 'classification' ? 'active blue' : '' ?>">Classifications</li></a>
		<a href="?settings=quick_action"><li class="<?= $_GET['settings'] == 'quick_action' ? 'active blue' : '' ?>">Quick Action Icons</li></a>
	</ul>
</div>

<div class="scale-to-fill has-main-screen hide-titles-mob" style="overflow: hidden;">
	<div class="main-screen standard-body form-horizontal">
		<div class="standard-body-title">
			<h3><?= $title ?></h3>
		</div>

		<div class="standard-body-content" style="padding: 1em;">
			<?php include($include_file); ?>
		</div>
	</div>
</div>
