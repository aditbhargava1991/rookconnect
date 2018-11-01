<?php $db_tabs = explode(',',get_config($dbc, 'staff_tabs')); ?>
<script>
$(document).ready(function() {
	$('#settings_accordions .panel-heading').click(loadPanel);
});
function loadPanel() {
	$('#settings_accordions .panel-body').html('Loading...');
	body = $(this).closest('.panel').find('.panel-body');
	$.ajax({
		url: $(body).data('file'),
		response: 'html',
		success: function(response) {
			$(body).html(response);
		}
	});
}
</script>
<div id='settings_accordions' class='sidebar show-on-mob panel-group block-panels col-xs-12'>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_regions">
					Dashboard &amp; Tabs<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_regions" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_dashboard.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_locations">
					Staff Fields<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_locations" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_fields.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_classifications">
					Profile Access<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_classifications" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_profile.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_titles">
					Positions<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_titles" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_positions.php">
				Loading...
			</div>
		</div>
	</div>
	<!--<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_import">
					Reminders<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_import" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_reminders.php">
				Loading...
			</div>
		</div>
	</div>-->
	<?php if(in_array('probation',$db_tabs)) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_probation">
						Staff on Probation Settings<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_probation" class="panel-collapse collapse">
				<div class="panel-body" data-file="field_config_probation.php">
					Loading...
				</div>
			</div>
		</div>
	<?php } ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_tabs">
					Staff Categories<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_tabs" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_categories.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_lock_alerts">
					Lock Alerts<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_lock_alerts" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_lock_alerts.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_fields">
					Import<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_fields" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_import.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_additions">
					Business Card Templates<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_additions" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_bus_card_templates.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_update_url">
					Update Profile Email<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_update_url" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_update_url.php">
				Loading...
			</div>
		</div>
	</div>
</div>
<!-- Sidebar -->
<div class="standard-collapsible hide-titles-mob tile-sidebar sidebar">
	<ul>
		<a href="?settings=dashboard"><li class="<?= empty($_GET['settings']) || $_GET['settings'] == 'dashboard' ? 'active blue' : '' ?>">Dashboard &amp; Tabs</li></a>
		<a href="?settings=fields"><li class="<?= $_GET['settings'] == 'fields' ? 'active blue' : '' ?>">Staff Fields</li></a>
		<a href="?settings=mandatory_fields"><li class="<?= $_GET['settings'] == 'mandatory_fields' ? 'active blue' : '' ?>">Staff Mandatory Fields</li></a>
		<a href="?settings=profile"><li class="<?= $_GET['settings'] == 'profile' ? 'active blue' : '' ?>">Profile Access</li></a>
		<a href="?settings=positions"><li class="<?= $_GET['settings'] == 'positions' ? 'active blue' : '' ?>">Positions</li></a>
		<!-- <a href="?settings=reminders"><li class="<?= $_GET['settings'] == 'reminders' ? 'active blue' : '' ?>">Reminders</li></a> -->
		<?php if(in_array('probation',$db_tabs)) { ?>
			<a href="?settings=probation"><li class="<?= $_GET['settings'] == 'probation' ? 'active blue' : '' ?>">Staff on Probation</li></a>
		<?php } ?>
		<a href="?settings=categories"><li class="<?= $_GET['settings'] == 'categories' ? 'active blue' : '' ?>">Staff Categories</li></a>
		<a href="?settings=locks"><li class="<?= $_GET['settings'] == 'locks' ? 'active blue' : '' ?>">Lock Alerts</li></a>
		<a href="?settings=import"><li class="<?= $_GET['settings'] == 'import' ? 'active blue' : '' ?>">Import</li></a>
		<a href="?settings=text_templates"><li class="<?= $_GET['settings'] == 'text_templates' ? 'active blue' : '' ?>">Text Editor Templates</li></a>
		<a href="?settings=bus_card"><li class="<?= $_GET['settings'] == 'bus_card' ? 'active blue' : '' ?>">Business Card Templates</li></a>
		<a href="?settings=update_url"><li class="<?= $_GET['settings'] == 'update_url' ? 'active blue' : '' ?>">Update Profile Email</li></a>
	</ul>
</div>
<div class='scale-to-fill has-main-screen tile-content hide-titles-mob'>
	<div class="main-screen override-main-screen standard-body" style="height: inherit;">
		<?php switch($_GET['settings']) {
		case 'fields':
			$body_title = 'Staff Fields';
			$include_file = 'field_config_fields.php';
			break;
		case 'mandatory_fields':
			$body_title = 'Staff Mandatory Fields';
			$include_file = 'field_config_mandatory_fields.php';
			break;
		case 'profile':
			$body_title = 'Profile Access';
			$include_file = 'field_config_profile.php';
			break;
		case 'positions':
			$body_title = 'Positions';
			$include_file = 'field_config_positions.php';
			break;
		case 'reminders':
			$body_title = 'Reminders';
			$include_file = 'field_config_reminders.php';
			break;
		case 'probation':
			$body_title = 'Probation';
			$include_file = 'field_config_probation.php';
			break;
		case 'categories':
			$body_title = 'Staff Categories';
			$include_file = 'field_config_categories.php';
			break;
		case 'import':
			$body_title = 'Import';
			$include_file = 'field_config_import.php';
			break;
		case 'bus_card':
			$body_title = 'Business Card Templates';
			$include_file = 'field_config_bus_card_templates.php';
			break;
		case 'locks':
			$body_title = 'Lock Alerts';
			$include_file = 'field_config_lock_alerts.php';
			break;
		case 'text_templates':
			$body_title = 'Text Editor Templates';
			$include_file = 'field_config_text_templates.php';
			break;
		case 'update_url':
			$body_title = 'Update Profile Email';
			$include_file = 'field_config_update_url.php';
			break;
		case 'dashboard':
		default:
			$body_title = 'Dashboard & Tabs';
			$include_file = 'field_config_dashboard.php';
			break;
		} ?>
		<div class='standard-body-title'>
			<h3><?= $body_title ?></h3>
		</div>
		<div class='standard-dashboard-body-content pad-top pad-left pad-right'>
			<?php include($include_file); ?>
		</div>
	</div>
</div>
