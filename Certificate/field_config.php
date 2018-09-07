<script>
var contact_type = '';
$(document).ready(function() {
	$('.panel-heading').click(loadPanel);
	$(window).resize(function() {
		var available_height = window.innerHeight - $(footer).outerHeight() - $('.main-screen .main-screen').offset().top;
		if(available_height > 200) {
			$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
			$('ul.sidebar').outerHeight(available_height).css('overflow-y','auto');
		}
	}).resize();
});
function loadPanel() {
	$('.panel-body').html('Loading...');
	body = $(this).closest('.panel').find('.panel-body');
	$.ajax({
		url: '../Certificate/'+$(body).data('file'),
		data: { folder: '<?= FOLDER_NAME ?>', type: contact_type },
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
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_dashboard">
					Dashboard<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_dashboard" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_dashboard.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_types">
					Certificate Types<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_types" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_certificate_types.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_categories">
					Categories<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_categories" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_categories.php">
				Loading...
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_email">
					Email Settings<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_subtab_email" class="panel-collapse collapse">
			<div class="panel-body" data-file="field_config_additions.php">
				Loading...
			</div>
		</div>
	</div>
</div>
<div class="tile-sidebar inherit-height sidebar sidebar-override double-gap-top hide-titles-mob collapsible">
	<ul>
		<a href="?settings=fields"><li class="<?= $_GET['settings'] == 'fields' ? 'active blue' : '' ?>">Fields</li></a>
		<a href="?settings=dashboard"><li class="<?= $_GET['settings'] == 'dashboard' ? 'active blue' : '' ?>">Dashboard</li></a>
		<a href="?settings=certificate_types"><li class="<?= $_GET['settings'] == 'certificate_types' ? 'active blue' : '' ?>">Certificate Types</li></a>
		<a href="?settings=categories"><li class="<?= $_GET['settings'] == 'categories' ? 'active blue' : '' ?>">Categories</li></a>
		<a href="?settings=email"><li class="<?= $_GET['settings'] == 'email' ? 'active blue' : '' ?>">Email Settings</li></a>
	</ul>
</div>
<div class="main-content-screen scale-to-fill has-main-screen hide-titles-mob">
	<div class="main-screen override-main-screen form-horizontal">
		<?php switch($_GET['settings']) {
		case 'dashboard':
			include('field_config_dashboard.php');
			break;
		case 'email':
			include('field_config_email.php');
			break;
		case 'certificate_types':
			include('field_config_certificate_types.php');
			break;
		case 'categories':
			include('field_config_categories.php');
			break;
		default:
		case 'fields':
			include('field_config_fields.php');
			break;
		} ?>
	</div>
</div>