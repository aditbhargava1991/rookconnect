<?php include_once('../include.php');
if(!empty($_GET['tile_name'])) {
	checkAuthorised(false,false,'documents_all_'.$_GET['tile_name']);
} else {
	checkAuthorised('documents_all');
}
include_once('document_settings.php');
if(empty($_GET['settings'])) {
	$_GET['settings'] = 'tabs';
} ?>

<script type="text/javascript">
$(document).ready(function() {
	$('#mobile_tabs .panel-heading').click(loadPanel);
});
function loadPanel() {
	var tab = $(this).data('type');
	var panel = $(this).closest('.panel').find('.panel-body');
	panel.html('Loading...');
	$.ajax({
		url: panel.data('file-name'),
		method: 'POST',
		response: 'html',
		success: function(response) {
			panel.html(response);
		    var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?settings='+tab;
		    window.history.pushState({path:newurl},'',newurl);
		}
	});
}
</script>

<div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" id="mobile_tabs">
	<?php if(empty($tile_name)) { ?>
		<div class="panel panel-default">
			<div data-type="tabs" class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_tabs">
						Tab Settings<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_tabs" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="field_config_tabs.php?settings=tabs">
					Loading...
				</div>
			</div>
		</div>
	<?php } ?>
	<?php foreach ($document_tabs as $type => $type_name) {
		switch($type) {
			case 'client_documents':
				$tab_file = 'client';
				break;
			case 'internal_documents':
				$tab_file = 'internal';
				break;
			case 'staff_documents':
				$tab_file = 'staff';
				break;
			case 'marketing_material':
				$tab_file = 'marketing';
				break;
			default:
				$tab_file = 'custom';
		} ?>
		<div class="panel panel-default">
			<div data-type="<?= $type ?>"  class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_<?= $type ?>">
						<?= $type_name ?><span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_<?= $type ?>" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="field_config_<?= $tab_file ?>.php?tile_name=<?= $tile_name ?>&settings=<?= $type ?>">
					Loading...
				</div>
			</div>
		</div>
	<?php } ?>
</div>

<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
	<ul>
		<li><a href="?tile_name=<?= $tile_name ?>&tab=<?= $_GET['tab'] ?>">Back to Dashboard</a></li>
		<?php if(empty($tile_name)) { ?>
			<li class="<?= $_GET['settings'] == 'tabs' ? 'active blue' : '' ?>"><a href="?tile_name=<?= $tile_name ?>&settings=tabs">Tab Settings</a></li>
		<?php } ?>
		<?php foreach ($document_tabs as $type => $type_name) { ?>
			<li class="<?= $_GET['settings'] == $type ? 'active blue' : '' ?>"><a href="?tile_name=<?= $tile_name ?>&settings=<?= $type ?>"><?= $type_name ?> Settings</a></li>
		<?php } ?>
	</ul>
</div>

<div class="scale-to-fill has-main-screen hide-titles-mob">
	<div class="main-screen standard-body form-horizontal">
		<div class="standard-body-title">
			<h3><?= $_GET['settings'] == 'tabs' ? 'Tab Settings' : $document_tabs[$_GET['settings']].' Settings' ?></h3>
		</div>

		<div class="standard-body-content" style="padding: 1em;">
			<?php if($_GET['settings'] == 'client_documents') {
				include('field_config_client.php');
			} else if($_GET['settings'] == 'staff_documents') {
				include('field_config_staff.php');
			} else if($_GET['settings'] == 'internal_documents') {
				include('field_config_internal.php');
			} else if($_GET['settings'] == 'marketing_material') {
				include('field_config_marketing.php');
			} else if($_GET['settings'] == 'tabs') {
				include('field_config_tabs.php');
			} else {
				include('field_config_custom.php');
			}?>
		</div>
	</div>
</div>