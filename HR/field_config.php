<div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible">
    <ul>
        <a href="?settings=tabs"><li class="<?= $_GET['settings'] == 'tabs' ? 'active blue' : '' ?>">HR Categories</li></a>
        <a href="?settings=fields"><li class="<?= $_GET['settings'] == 'fields' ? 'active blue' : '' ?>">Fields</li></a>
        <a href="?settings=manuals"><li class="<?= $_GET['settings'] == 'manuals' ? 'active blue' : '' ?>">Manuals</li></a>
        <a href="?settings=forms"><li class="<?= $_GET['settings'] == 'forms' ? 'active blue' : '' ?>">Forms</li></a>
        <a href="?settings=form_design"><li class="<?= $_GET['settings'] == 'form_design' ? 'active blue' : '' ?>">Form Design</li></a>
        <a href="?settings=summary"><li class="<?= $_GET['settings'] == 'summary' ? 'active blue' : '' ?>">Summary</li></a>
        <a href="?settings=performance_reviews"><li class="<?= $_GET['settings'] == 'performance_reviews' ? 'active blue' : '' ?>">Performance Reviews</li></a>
        <a href="?settings=request_update"><li class="<?= $_GET['settings'] == 'request_update' ? 'active blue' : '' ?>">Request an Update</li></a>
    </ul>
</div>
<div class='scale-to-fill has-main-screen hide-titles-mob'>
	<div class='main-screen form-horizontal'>
		<?php switch($_GET['settings']) {
		case 'request_update':
			include('field_config_request_update.php');
			break;
		case 'performance_reviews':
			include('field_config_performance_reviews.php');
			break;
		case 'forms':
			include('field_config_forms.php');
			break;
		case 'form_design':
			include('field_config_form_design.php');
			break;
		case 'fields':
			include('field_config_fields.php');
			break;
		case 'manuals':
			include('field_config_manuals.php');
			break;
		case 'summary':
			include('field_config_summary.php');
			break;
		case 'tabs':
		default:
			include('field_config_tabs.php');
			break;
		} ?>
	</div>
</div>