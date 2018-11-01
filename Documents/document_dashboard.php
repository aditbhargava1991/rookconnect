<?php include_once('../include.php');
if(!empty($_GET['tile_name'])) {
	checkAuthorised(false,false,'documents_all_'.$_GET['tile_name']);
} else {
	checkAuthorised('documents_all');
}
include_once('document_settings.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
	$('#mobile_tabs .panel-heading').click(loadPanel);
});
function loadPanel() {
	var panel = $(this).closest('.panel').find('.panel-body');
	panel.html('Loading...');
	$.ajax({
		url: panel.data('file-name'),
		method: 'POST',
		response: 'html',
		success: function(response) {
			panel.html(response);
		}
	});
}
</script>

<div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" id="mobile_tabs">
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
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_<?= $type ?>">
						<?= $type_name ?><span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_<?= $type ?>" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="dashboard_<?= $tab_file ?>.php?tile_name=<?= $tile_name ?>&tab=<?= $type ?>">
					Loading...
				</div>
			</div>
		</div>
	<?php } ?>
</div>

<?php if($dashboard_layout != 'tile') { ?>
	<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
		<ul>
	        <form action="" method="GET">
				<li class="standard-sidebar-searchbox">
					<input type="text" name="search_query" class="form-control" placeholder="Search <?= $tab_title ?>" value="<?= $_GET['search_query'] ?>">
					<input type="hidden" name="tab" value="<?= $_GET['tab'] ?>">
					<input type="hidden" name="search_type" value="<?= $_GET['search_type'] ?>">
					<input type="hidden" name="search_category" value="<?= $_GET['search_category'] ?>">
		            <input type="submit" value="Search" class="btn brand-btn" name="search_submit" style="display:none;" />
		        </li>
			</form>
			<?php foreach ($document_tabs as $type => $type_name) { ?>
				<a href="?tile_name=<?= $tile_name ?>&tab=<?= $type ?>"><li class="<?= $tab == $type ? 'active blue' : '' ?>"><?= $type_name ?></li></a>
				<?php if($tab == $type) { ?>
					<ul style="margin-top: 0;">
						<?php if($tab == 'staff_documents') { ?>
							<li class="sidebar-higher-level"><a class="cursor-hand <?= empty($_GET['search_staff']) ? 'collapsed' : '' ?>" data-toggle="collapse" data-target="#filter_staff">Staff<span class="arrow"></span></a>
								<ul class="<?= empty($_GET['search_staff']) ? 'collapse' : 'collapsed' ?>" id="filter_staff">
									<?php $search_staffs = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` = 1 AND `show_hide_user` = 1"),MYSQLI_ASSOC));
									foreach ($search_staffs as $search_staff) { ?>
										<li class="<?= $search_staff == $_GET['search_staff'] ? 'active blue' : '' ?>"><a href="?tile_name=<?= $tile_name ?>&tab=<?= $_GET['tab'] ?>&search_type=<?= $_GET['search_type'] ?>&search_category=<?= $_GET['search_category'] ?>&search_staff=<?= $search_staff != $_GET['search_staff'] ? $search_staff : '' ?>&search_query=<?= $_GET['search_query'] ?>"><?= get_contact($dbc, $search_staff) ?></a></li>
									<?php } ?>
								</ul>
							</li>
						<?php } ?>
						<li class="sidebar-higher-level"><a class="cursor-hand <?= empty($_GET['search_type']) ? 'collapsed' : '' ?>" data-toggle="collapse" data-target="#filter_type">Type<span class="arrow"></span></a>
							<ul class="<?= empty($_GET['search_type']) ? 'collapse' : 'collapsed' ?>" id="filter_type">
								<?php $search_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`$tab_table_type`) FROM `$tab_table` WHERE `deleted` = 0 $custom_tab_query ORDER BY `$tab_table_type`"),MYSQLI_ASSOC);
								foreach ($search_types as $search_type) { ?>
									<li class="<?= $search_type[$tab_table_type] == $_GET['search_type'] ? 'active blue' : '' ?>"><a href="?tile_name=<?= $tile_name ?>&tab=<?= $_GET['tab'] ?>&search_type=<?= $search_type[$tab_table_type] != $_GET['search_type'] ? $search_type[$tab_table_type] : '' ?>&search_category=<?= $_GET['search_category'] ?>&search_staff=<?= $_GET['search_staff'] ?>&search_query=<?= $_GET['search_query'] ?>"><?= $search_type[$tab_table_type] ?></a></li>
								<?php } ?>
							</ul>
						</li>
						<li class="sidebar-higher-level"><a class="cursor-hand <?= empty($_GET['search_category']) ? 'collapsed' : '' ?>" data-toggle="collapse" data-target="#filter_category">Category<span class="arrow"></span></a>
							<ul class="<?= empty($_GET['search_category']) ? 'collapse' : 'collapsed' ?>" id="filter_category">
								<?php $search_categories = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`$tab_table_category`) FROM `$tab_table` WHERE `deleted` = 0 $custom_tab_query ORDER BY `$tab_table_category`"),MYSQLI_ASSOC);
								foreach ($search_categories as $search_category) { ?>
									<li class="<?= $search_category[$tab_table_category] == $_GET['search_category'] ? 'active blue' : '' ?>"><a href="?tile_name=<?= $tile_name ?>&tab=<?= $_GET['tab'] ?>&search_type=<?= $_GET['search_type'] ?>&search_category=<?= $search_category[$tab_table_category] != $_GET['search_category'] ? $search_category[$tab_table_category] : '' ?>&search_staff=<?= $_GET['search_staff'] ?>&search_query=<?= $_GET['search_query'] ?>"><?= $search_category[$tab_table_category] ?></a></li>
								<?php } ?>
							</ul>
						</li>
					</ul>
				<?php } ?>
			<?php } ?>
		</ul>
	</div>
<?php } ?>

<?php if($dashboard_layout == 'tile' && empty($_GET['tab'])) { ?>
	<div class="scale-to-fill has-main-screen hide-titles-mob">
		<div class="main-screen standard-body form-horizontal">
			<div class="standard-body-content" style="padding: 1em;">
				<?php foreach($document_tabs as $type => $type_name) { ?>
					<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12"><a href="?tile_name=<?= $tile_name ?>&tab=<?= $type ?>"><?= $type_name ?></a></div>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } else { ?>
	<div class="scale-to-fill has-main-screen hide-titles-mob">
		<div class="main-screen standard-body form-horizontal">
			<div class="standard-body-title">
				<h3><?= $tab_title ?></h3>
			</div>

			<div class="standard-body-content" style="padding: 1em;">
				<?php if($dashboard_layout == 'tile' && empty($tile_name)) { ?>
					<a href="?tile_name=<?= $tile_name ?>" class="btn brand-btn gap-bottom">Back</a>
				<?php } ?>
				<?php if($_GET['tab'] == 'client_documents') {
					include('dashboard_client.php');
				} else if($_GET['tab'] == 'staff_documents') {
					include('dashboard_staff.php');
				} else if($_GET['tab'] == 'internal_documents') {
					include('dashboard_internal.php');
				} else if($_GET['tab'] == 'marketing_material') {
					include('dashboard_marketing.php');
				} else {
					include('dashboard_custom.php');
				} ?>
			</div>
		</div>
	</div>
<?php } ?>