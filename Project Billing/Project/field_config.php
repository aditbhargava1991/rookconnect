<script>
$(document).ready(function() {
	$('.panel-heading').click(loadPanel);
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
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_status">
					<?= PROJECT_NOUN ?> Status<span class="glyphicon glyphicon-plus"></span>
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
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_status">
					<?= PROJECT_NOUN ?> Path Template<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_status" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_path_template.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_admin">
					Administration<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_admin" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_administration.php">
				Loading...
			</div>
		</div>
	</div>
</div>
<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
	<ul>
		<a href="?settings=fields"><li class="<?= empty($_GET['settings']) || $_GET['settings'] == 'fields' ? 'active blue' : '' ?>">Activate Fields</li></a>
		<a href="?settings=tabs"><li class="<?= $_GET['settings'] == 'tabs' ? 'active blue' : '' ?>">Activate Tabs</li></a>
		<a href="?settings=types"><li class="<?= $_GET['settings'] == 'types' ? 'active blue' : '' ?>"><?= PROJECT_NOUN ?> Types</li></a>
		<a href="?settings=tile"><li class="<?= $_GET['settings'] == 'tile' ? 'active blue' : '' ?>">Tile Settings</li></a>
		<a href="?settings=status"><li class="<?= $_GET['settings'] == 'status' ? 'active blue' : '' ?>"><?= PROJECT_NOUN ?> Status</li></a>
		<a href="?settings=path"><li class="<?= $_GET['settings'] == 'path' ? 'active blue' : '' ?>"><?= PROJECT_NOUN ?> Path Templates</li></a>
		<a href="?settings=quick"><li class="<?= $_GET['settings'] == 'quick' ? 'active blue' : '' ?>">Quick Action Icons</li></a>
		<a href="?settings=administration"><li class="<?= $_GET['settings'] == 'administration' ? 'active blue' : '' ?>">Administration</li></a>
	</ul>
</div>
<?php switch($_GET['settings']) {
	case 'fields':
		$body_title = 'Activate Fields';
		break;
	case 'tabs':
		$body_title = 'Activate Tabs';
		break;
	case 'types':
		$body_title = PROJECT_NOUN.' Types';
		break;
	case 'tile':
		$body_title = 'Tile Settings';
		break;
	case 'status':
		$body_title = PROJECT_NOUN.' Status';
		break;
	case 'path':
		$body_title = PROJECT_NOUN.' Path Templates';
		break;
	case 'quick':
		$body_title = 'Quick Action Icons';
		break;
	case 'administration':
		$body_title = 'Administration';
		break;
} ?>
<div class="scale-to-fill has-main-screen hide-titles-mob">
	<div class='main-screen standard-body'>
		<div class='standard-body-title'>
			<h3><?= $body_title ?></h3>
		</div>
		<div class='standard-body-content pad-top pad-left pad-right'>
			<?php switch($_GET['settings']) {
			case 'path':
				include('field_config_path_template.php');
				break;
			case 'status':
				include('field_config_status.php');
				break;
			case 'tile':
				include('field_config_tile.php');
				break;
			case 'types':
				include('field_config_types.php');
				break;
			case 'tabs':
				include('field_config_tabs.php');
				break;
			case 'quick':
				include('field_config_flags.php');
				break;
			case 'administration':
				include('field_config_administration.php');
				break;
			default:
				include('field_config_fields.php');
				break;
			} ?>
		</div>
	</div>
</div>