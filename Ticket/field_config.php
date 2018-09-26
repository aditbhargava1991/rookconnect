<?php
switch($_GET['settings']) {
	case 'dashboard':
		$page_title = 'Dashboard Fields';
		break;
	case 'action':
		$page_title = 'Action Mode Fields';
		break;
	case 'status_fields':
		$page_title = 'Status Fields';
		break;
	case 'overview':
		$page_title = 'Overview Fields';
		break;
	case 'pdf':
		$page_title = 'PDF Options';
		break;
	case 'forms':
		$page_title = 'PDF Forms';
		break;
	case 'tile':
		$page_title = 'Tile Settings';
		break;
	case 'tile_split':
		$page_title = 'Tile Splitting';
		break;
	case 'types':
		$page_title = TICKET_NOUN.' Tabs';
		break;
	case 'alerts':
		$page_title = TICKET_NOUN.' Tab Alerts';
		break;
	case 'status':
		$page_title = 'Statuses';
		break;
	case 'flags':
		$page_title = 'Quick Action Flags';
		break;
	case 'security':
		$page_title = 'Roles & Security';
		break;
	case 'groups':
		$page_title = 'Staff Groups';
		break;
	case 'time':
		$page_title = 'Time Sheets';
		break;
	case 'ticket_log':
		$page_title = TICKET_NOUN.' Log';
		break;
	case 'importing':
		$page_title = 'Import Templates';
		break;
	case 'intake':
		$page_title = 'Intake Fields';
		break;
	case 'summary_security':
		$page_title = 'Summary Access';
		break;
	case 'administration':
		$page_title = 'Administration';
		break;
	case 'accounting':
		$page_title = 'Accounting';
		break;
	case 'tasks':
		$page_title = 'Staff Tasks';
		break;
	case 'manifests':
		$page_title = 'Manifest Fields';
		break;
	case 'mandatory_fields':
		$page_title = TICKET_NOUN . ' Mandatory Fields';
		break;
	default:
		$page_title = TICKET_NOUN.' Fields - '.(empty($_GET['type_name']) ? 'All '.TICKET_NOUN : $ticket_tabs[$_GET['type_name']]).' Fields';;
		break;
	}
?>
<script>
$(document).ready(function() {
	$('.panel-heading').not('.no_load').click(loadPanel);
});
function loadPanel() {
	$('.panel-body').html('Loading...');
	body = $(this).closest('.panel').find('.panel-body');
	$.ajax({
		url: $(body).data('file'),
		method: 'POST',
		response: 'html',
		success: function(response) {
			$(body).html(response);
		}
	});
}
</script>
<div id='settings_accordions' class='sidebar show-on-mob panel-group block-panels col-xs-12'>
	<?php if(empty($_GET['tile_name'])) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_dashboard">
						Dashboard Fields<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_dashboard" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_dashboard.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_fields">
						<?= TICKET_NOUN ?> Fields<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_fields" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_fields.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_mandatory_fields">
						<?= TICKET_NOUN ?> Mandatory Fields<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_fields" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_mandatory_fields.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_action">
						Action Mode Fields<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_action" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_action.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_overview">
						Overview Fields<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_overview" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_overview.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_status_fields">
						Status Fields<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_status_fields" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_status_fields.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_intake">
						Intake Fields<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_intake" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_intake.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_summary_security">
						Summary Access<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_summary_security" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_summary_security.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_manifests">
						Manifest Fields<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_summary_security" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_manifests.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_pdf">
						PDF Options<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_pdf" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_pdf.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_ticket_types">
						<?= TICKET_NOUN ?> Tabs<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_ticket_types" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_types.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_ticket_alerts">
						<?= TICKET_NOUN ?> Tab Alerts<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_ticket_alerts" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_alerts.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_status">
						Statuses<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_status" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_status.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_tile">
						Tile Settings<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_tile" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_tile.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_administration">
						Administration<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_administration" class="panel-collapse collapse">
				<div class="panel-body" data-file="../Project/field_config_administration.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_accounting">
						Accounting<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_accounting" class="panel-collapse collapse">
				<div class="panel-body" data-file="../Project/field_config_accounting.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_quick_action">
						Quick Action Icons<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_quick_action" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_flags.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_security">
						Roles &amp; Security<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_security" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_security.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_staff_groups">
						Staff Groups<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_staff_groups" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_staff_groups.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_staff_tasks">
						Staff Tasks<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_staff_tasks" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_staff_tasks.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_time">
						Time Tracking<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_time" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_timesheets.php">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_ticket_log">
						<?= TICKET_NOUN ?> Log<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_ticket_log" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_ticket_log.php">
					Loading...
				</div>
			</div>
		</div>
	<?php } else { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_fields">
						<?= $ticket_tabs[$_GET['tile_name']] ?> Fields<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_fields" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_fields.php">
					Loading...
				</div>
			</div>
		</div>
	<?php } ?>
</div>
<div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible">
	<ul>
		<?php if(empty($_GET['tile_name'])) { ?>
			<a href="?settings=dashboard"><li class="<?= $_GET['settings'] == 'dashboard' ? 'active blue' : '' ?>">Dashboard Fields</li></a>
			<a href="?settings=fields"><li class="<?= empty($_GET['settings']) || $_GET['settings'] == 'fields' ? 'active blue' : '' ?>"><?= TICKET_NOUN ?> Fields</li></a>
			<a href="?settings=mandatory_fields"><li class="<?= $_GET['settings'] == 'mandatory_fields' ? 'active blue' : '' ?>"><?= TICKET_NOUN ?> Mandatory Fields</li></a>
			<a href="?settings=action"><li class="<?= $_GET['settings'] == 'action' ? 'active blue' : '' ?>">Action Mode Fields</li></a>
			<a href="?settings=overview"><li class="<?= $_GET['settings'] == 'overview' ? 'active blue' : '' ?>">Overview Fields</li></a>
			<a href="?settings=status_fields"><li class="<?= $_GET['settings'] == 'status_fields' ? 'active blue' : '' ?>">Status Fields</li></a>
			<a href="?settings=intake"><li class="<?= $_GET['settings'] == 'intake' ? 'active blue' : '' ?>">Intake Fields</li></a>
			<a href="?settings=summary_security"><li class="<?= $_GET['settings'] == 'summary_security' ? 'active blue' : '' ?>">Summary Access</li></a>
			<a href="?settings=manifests"><li class="<?= $_GET['settings'] == 'manifests' ? 'active blue' : '' ?>">Manifest Fields</li></a>
			<a href="?settings=pdf"><li class="<?= $_GET['settings'] == 'pdf' ? 'active blue' : '' ?>">PDF Options</li></a>
			<a href="?settings=types"><li class="<?= $_GET['settings'] == 'types' ? 'active blue' : '' ?>"><?= TICKET_NOUN ?> Tabs</li></a>
			<a href="?settings=alerts"><li class="<?= $_GET['settings'] == 'alerts' ? 'active blue' : '' ?>"><?= TICKET_NOUN ?> Tab Alerts</li></a>
			<a href="?settings=status"><li class="<?= $_GET['settings'] == 'status' ? 'active blue' : '' ?>">Statuses</li></a>
			<a href="?settings=tile"><li class="<?= $_GET['settings'] == 'tile' ? 'active blue' : '' ?>">Tile Settings</li></a>
			<a href="?settings=tile_split"><li class="<?= $_GET['settings'] == 'tile_split' ? 'active blue' : '' ?>">Tile Splitting</li></a>
			<a href="?settings=administration"><li class="<?= $_GET['settings'] == 'administration' ? 'active blue' : '' ?>">Administration</li></a>
			<a href="?settings=accounting"><li class="<?= $_GET['settings'] == 'accounting' ? 'active blue' : '' ?>">Accounting</li></a>
			<a href="?settings=flags"><li class="<?= $_GET['settings'] == 'flags' ? 'active blue' : '' ?>">Quick Action Icons</li></a>
			<a href="?settings=security"><li class="<?= $_GET['settings'] == 'security' ? 'active blue' : '' ?>">Roles &amp; Security</li></a>
			<a href="?settings=groups"><li class="<?= $_GET['settings'] == 'groups' ? 'active blue' : '' ?>">Staff Groups</li></a>
			<a href="?settings=tasks"><li class="<?= $_GET['settings'] == 'tasks' ? 'active blue' : '' ?>">Staff Tasks</li></a>
			<a href="?settings=time"><li class="<?= $_GET['settings'] == 'time' ? 'active blue' : '' ?>">Time Sheets</li></a>
			<a href="?settings=forms"><li class="<?= $_GET['settings'] == 'forms' ? 'active blue' : '' ?>">PDF Forms</li></a>
			<a href="?settings=ticket_log"><li class="<?= $_GET['settings'] == 'ticket_log' ? 'active blue' : '' ?>"><?= TICKET_NOUN ?> Log</li></a>
			<a href="?settings=importing"><li class="<?= $_GET['settings'] == 'importing' ? 'active blue' : '' ?>">Import Templates</li></a>
			<a href="?settings=field_security"><li class="<?= $_GET['settings'] == 'field_security' ? 'active blue' : '' ?>">Security Settings</li></a>
			<a href="?settings=checklist"><li class="<?= $_GET['settings'] == 'checklist' ? 'active blue' : '' ?>">Checklist</li></a>
		<?php } else { ?>
			<a href="?settings=fields&tile_name=<?= $_GET['tile_name'] ?>"><li class="<?= empty($_GET['settings']) || $_GET['settings'] == 'fields' ? 'active blue' : '' ?>"><?= $ticket_tabs[$_GET['tile_name']] ?> Fields</li></a>
		<?php } ?>
	</ul>
</div>
<div class='scale-to-fill has-main-screen hide-titles-mob'>
	<div class='main-screen standard-body form-horizontal'>
		<div class="standard-body-title">
			<h3><?= $page_title ?></h3>
		</div>
		<div class="standard-body-content pad-top" style="padding: 5px;">
		<?php switch($_GET['settings']) {
			case 'dashboard':
				include('field_config_dashboard.php');
				break;
			case 'intake':
				include('field_config_intake.php');
				break;
			case 'summary_security':
				include('field_config_summary_security.php');
				break;
			case 'overview':
				include('field_config_overview.php');
				break;
			case 'action':
				include('field_config_action.php');
				break;
			case 'status_fields':
				include('field_config_status_fields.php');
				break;
			case 'pdf':
				include('field_config_pdf.php');
				break;
			case 'tile':
				include('field_config_tile.php');
				break;
			case 'tile_split':
				include('field_config_tile_split.php');
				break;
			case 'types':
				include('field_config_types.php');
				break;
			case 'alerts':
				include('field_config_alerts.php');
				break;
			case 'status':
				include('field_config_status.php');
				break;
			case 'flags':
				include('field_config_flags.php');
				break;
			case 'security':
				include('field_config_security.php');
				break;
			case 'groups':
				include('field_config_groups.php');
				break;
			case 'time':
				include('field_config_timesheets.php');
				break;
			case 'ticket_log':
				include('field_config_ticket_log.php');
				break;
			case 'forms':
				include('field_config_pdf_forms.php');
				break;
			case 'importing':
				include('field_config_import_templates.php');
				break;
			case 'administration':
				include('../Project/field_config_administration.php');
				break;
			case 'accounting':
				include('field_config_accounting.php');
				break;
			case 'tasks':
				include('field_config_staff_tasks.php');
				break;
			case 'manifests':
				include('field_config_manifests.php');
				break;
			case 'field_security':
				include('field_config_field_security.php');
				break;
			case 'checklist':
				include('field_config_checklist.php');
				break;
			case 'mandatory_fields':
				$mandatory = 1;
				include('field_config_fields.php');
				break;
			default:
				include('field_config_fields.php');
				break;
			} ?>
		</div>
	</div>
</div>
