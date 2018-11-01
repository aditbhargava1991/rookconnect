<script>
$(document).ready(function() {
	$('.panel-heading').click(loadPanel);
});
function loadPanel() {
	body = $(this).closest('.panel').find('.panel-body');
    if($(body).data('file') !== undefined) {
        $('.panel-body').html('Loading...');
        $.ajax({
            url: $(body).data('file'),
            method: 'POST',
            response: 'html',
            success: function(response) {
                $(body).html(response);
                loadingOverlayHide();
            }
        });
    } else {
        loadingOverlayHide();
    }
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
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_paths">
					<?= PROJECT_NOUN ?> Path Template<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_paths" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_path_template.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_contacts">
					<?= PROJECT_NOUN ?> <?= CONTACTS_TILE ?><span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_contacts" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_contacts.php">
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
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_groups">
					Teams<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_groups" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_groups.php">
				Loading...
			</div>
		</div>
	</div>
</div>
<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
	<ul>
		<a href="projects.php"><li>Back to Dashboard</li></a>
		<a href="?settings=fields"><li class="<?= empty($_GET['settings']) || $_GET['settings'] == 'fields' ? 'active blue' : '' ?>">Activate Fields</li></a>
		<a href="?settings=mandatory_fields"><li class="<?= empty($_GET['settings']) || $_GET['settings'] == 'mandatory_fields' ? 'active blue' : '' ?>">Mandatory Fields</li></a>
		<a href="?settings=tabs"><li class="<?= $_GET['settings'] == 'tabs' ? 'active blue' : '' ?>">Activate Tabs</li></a>
		<a href="?settings=types"><li class="<?= $_GET['settings'] == 'types' ? 'active blue' : '' ?>"><?= PROJECT_NOUN ?> Types</li></a>
		<a href="?settings=tile"><li class="<?= $_GET['settings'] == 'tile' ? 'active blue' : '' ?>">Tile Settings</li></a>
		<a href="?settings=status"><li class="<?= $_GET['settings'] == 'status' ? 'active blue' : '' ?>"><?= PROJECT_NOUN ?> Status</li></a>
		<a href="?settings=path"><li class="<?= $_GET['settings'] == 'path' ? 'active blue' : '' ?>"><?= PROJECT_NOUN ?> Path Templates</li></a>
		<a href="?settings=contacts"><li class="<?= $_GET['settings'] == 'contacts' ? 'active blue' : '' ?>"><?= PROJECT_NOUN ?> <?= CONTACTS_TILE ?></li></a>
		<a href="?settings=quick"><li class="<?= $_GET['settings'] == 'quick' ? 'active blue' : '' ?>">Quick Action Icons</li></a>
		<a href="?settings=administration"><li class="<?= $_GET['settings'] == 'administration' ? 'active blue' : '' ?>">Administration</li></a>
		<a href="?settings=groups"><li class="<?= $_GET['settings'] == 'groups' ? 'active blue' : '' ?>">Teams</li></a>
	</ul>
</div>
<?php switch($_GET['settings']) {
	case 'fields':
		$body_title = 'Activate Fields';
		break;
	case 'mandatory_fields':
		$body_title = 'Mandatory Fields';
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
	case 'contacts':
		$body_title = PROJECT_NOUN.' '.CONTACTS_TILE;
		break;
	case 'quick':
		$body_title = 'Quick Action Icons';
		break;
	case 'administration':
		$body_title = 'Administration';
		break;
	case 'groups':
		$body_title = 'Teams';
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
			case 'contacts':
				include('field_config_contacts.php');
				break;
			case 'quick':
				include('field_config_flags.php');
				break;
			case 'administration':
				include('field_config_administration.php');
				break;
			case 'mandatory_fields':
				include('field_config_mandatory_fields.php');
				break;
			case 'groups':
				include('field_config_groups.php');
				break;
			default:
				include('field_config_fields.php');
				break;
			} ?>
		</div>
	</div>
</div>
