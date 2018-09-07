<ul class='sidebar hide-titles-mob collapsible' style='padding-left: 15px;'>
	<a href="?settings=tabs"><li class="<?= $_GET['settings'] == 'tabs' ? 'active blue' : '' ?>">Safety Tabs</li></a>
	<!--<a href="?settings=sites"><li class="<?= $_GET['settings'] == 'sites' ? 'active blue' : '' ?>">Sites</li></a>-->
	<a href="?settings=forms"><li class="<?= $_GET['settings'] == 'forms' ? 'active blue' : '' ?>">Forms</li></a>
	<a href="?settings=manuals"><li class="<?= $_GET['settings'] == 'manuals' ? 'active blue' : '' ?>">Manuals</li></a>
</ul>
<div class='scale-to-fill has-main-screen hide-titles-mob'>
	<div class='main-screen form-horizontal'>
		<?php switch($_GET['settings']) {
		case 'sites':
			include('field_config_sites.php');
			break;
		case 'forms':
			include('field_config_forms.php');
			break;
		case 'manuals':
			include('field_config_manuals.php');
			break;
		case 'tabs':
		default:
			include('field_config_safety_tabs.php');
			break;
		} ?>
	</div>
</div>