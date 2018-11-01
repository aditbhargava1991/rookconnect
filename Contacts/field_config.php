<script>
var contact_type = '';
$(document).ready(function() {
	$('.panel-heading').click(loadPanel);
	$(window).resize(function() {
		var available_height = window.innerHeight - $(footer).outerHeight() - $('.main-screen .main-screen').offset().top;
		if(available_height > 200) {
			$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
			$('.tile-sidebar').outerHeight(available_height).css('overflow-y','auto');
		}
	}).resize();
});
function loadPanel() {
	if(!$(this).hasClass('no_load')) {
		$('.panel-body').html('Loading...');
		body = $(this).closest('.panel').find('.panel-body');
		$.ajax({
			url: '../Contacts/'+$(body).data('file'),
			data: { folder: '<?= FOLDER_NAME ?>', type: contact_type },
			method: 'POST',
			response: 'html',
			success: function(response) {
				$(body).html(response);
			}
		});
	}
}
</script>
<div id='settings_accordions' class='sidebar show-on-mob panel-group block-panels col-xs-12'>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_tile">
					Tile Settings<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_tile" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_tile.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_regions">
					Regions<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_regions" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_regions.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_locations">
					Locations<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_locations" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_locations.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_classifications">
					Classifications<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_classifications" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_classifications.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_titles">
					Titles<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_titles" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_titles.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_tabs">
					Contact Categories<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_tabs" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_tabs.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_fields">
					Fields<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_fields" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_fields.php">
				Loading...
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_fields">
					Mandatory Fields<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_fields" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_mandatory_fields.php">
				Loading...
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_fields2">
					Subtab Sort Order<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_fields2" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_subtab_sort_order.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_additions">
					Profile Additions<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_additions" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_additions.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_id_card_fields">
					ID Card Fields<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_id_card_fields" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_id_card_fields.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_import">
					Import / Export<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_import" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_import.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_security">
					Security Settings<span class="glyphicon glyphicon-plus"></span>
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

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_update_quick">
					Quick Actions<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_update_quick" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_update_url.php">
				Loading...
			</div>
		</div>
	</div>


</div>
<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
    <ul class=''>
        <a href="?settings=tile"><li class="<?= $_GET['settings'] == 'tile' ? 'active blue' : '' ?>">Tile Settings</li>
        <a href="?settings=regions"><li class="<?= empty($_GET['settings']) || $_GET['settings'] == 'regions' ? 'active blue' : '' ?>">Regions</li></a>
        <a href="?settings=locations"><li class="<?= $_GET['settings'] == 'locations' ? 'active blue' : '' ?>">Locations</li></a>
        <a href="?settings=classifications"><li class="<?= $_GET['settings'] == 'classifications' ? 'active blue' : '' ?>">Classifications</li></a>
        <a href="?settings=titles"><li class="<?= $_GET['settings'] == 'titles' ? 'active blue' : '' ?>">Titles</li></a>
        <a href="?settings=tabs"><li class="<?= $_GET['settings'] == 'tabs' ? 'active blue' : '' ?>">Contact Categories</li></a>
        <a href="?settings=fields"><li class="<?= $_GET['settings'] == 'fields' ? 'active blue' : '' ?>">Fields</li></a>
				<a href="?settings=mandatory_fields"><li class="<?= $_GET['settings'] == 'mandatory_fields' ? 'active blue' : '' ?>">Mandatory Fields</li></a>
        <a href="?settings=subtab_sort_order"><li class="<?= $_GET['settings'] == 'subtab_sort_order' ? 'active blue' : '' ?>">Subtab Sort Order</li></a>
        <a href="?settings=dashboard"><li class="<?= $_GET['settings'] == 'dashboard' ? 'active blue' : '' ?>">Dashboard</li></a>
        <a href="?settings=summary"><li class="<?= $_GET['settings'] == 'summary' ? 'active blue' : '' ?>">Summary</li></a>
        <a href="?settings=additions"><li class="<?= $_GET['settings'] == 'additions' ? 'active blue' : '' ?>">Profile Additions</li></a>
        <a href="?settings=id_card_fields"><li class="<?= $_GET['settings'] == 'id_card_fields' ? 'active blue' : '' ?>">ID Card Fields</li></a>
        <a href="?settings=import"><li class="<?= $_GET['settings'] == 'import' ? 'active blue' : '' ?>">Import Contacts</li></a>
        <a href="?settings=security"><li class="<?= $_GET['settings'] == 'security' ? 'active blue' : '' ?>">Security Settings</li></a>
        <a href="?settings=quick_actions"><li class="<?= $_GET['settings'] == 'quick_actions' ? 'active blue' : '' ?>">Quick Actions</li></a>

        <a href="?settings=update_url"><li class="<?= $_GET['settings'] == 'update_url' ? 'active blue' : '' ?>">Update Profile Email</li></a>
        <?php if(tile_visible($dbc, 'vpl') && FOLDER_NAME == 'vendors') { ?>
	        <a href="?settings=vpl_tabs"><li class="<?= $_GET['settings'] == 'vpl_tabs' ? 'active blue' : '' ?>">Vendor Price List - Tabs</li></a>
	        <a href="?settings=vpl_fields"><li class="<?= $_GET['settings'] == 'vpl_fields' ? 'active blue' : '' ?>">Vendor Price List - Fields</li></a>
	        <a href="?settings=vpl_dashboard"><li class="<?= $_GET['settings'] == 'vpl_dashboard' ? 'active blue' : '' ?>">Vendor Price List - Dashboard</li></a>
	        <a href="?settings=vpl_impexp"><li class="<?= $_GET['settings'] == 'vpl_impexp' ? 'active blue' : '' ?>">Vendor Price List - Import/Export</li></a>
	        <a href="?settings=vpl_orderforms"><li class="<?= $_GET['settings'] == 'vpl_orderforms' ? 'active blue' : '' ?>">Vendor Price List - Order Forms</li></a>
        <?php } ?>
    </ul>
</div>
<div class='has-main-screen scale-to-fill hide-titles-mob'>
	<div class='main-screen standard-dashboard-body'>
		<?php switch($_GET['settings']) {
		case 'vpl_tabs':
			include('field_config_vpl_tabs.php');
			break;
		case 'vpl_fields':
			include('field_config_vpl_fields.php');
			break;
		case 'vpl_dashboard':
			include('field_config_vpl_dashboard.php');
			break;
		case 'vpl_impexp':
			include('field_config_vpl_impexp.php');
			break;
		case 'vpl_orderforms':
			include('field_config_vpl_orderforms.php');
			break;
		case 'tile':
			include('field_config_tile.php');
			break;
		case 'tabs':
			include('field_config_tabs.php');
			break;
		case 'locations':
			include('field_config_locations.php');
			break;
		case 'classifications':
			include('field_config_classifications.php');
			break;
		case 'titles':
			include('field_config_titles.php');
			break;
		case 'fields':
			include('field_config_fields.php');
			break;
		case 'mandatory_fields':
			include('field_config_mandatory_fields.php');
			break;
		case 'subtab_sort_order':
			include('field_config_subtab_sort_order.php');
			break;
		case 'dashboard':
			include('field_config_dashboard.php');
			break;
		case 'summary':
			include('field_config_summary.php');
			break;
		case 'additions':
			include('field_config_additions.php');
			break;
		case 'id_card_fields':
			include('field_config_id_card_fields.php');
			break;
		case 'import':
			include('field_config_import.php');
			break;
		case 'security':
			include('field_config_security.php');
			break;
		case 'quick_actions':
			include('field_config_quick_actions.php');
			break;
		case 'update_url':
			include('field_config_update_url.php');
			break;
		case 'regions':
		default:
			include('field_config_regions.php');
			break;
		} ?>
	</div>
</div>
