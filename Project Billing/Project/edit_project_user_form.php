<?php include_once('../include.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(isset($_GET['projectid'])) {
	$_GET['edit'] = $_GET['projectid'];
}
$tile = filter_var($_GET['tile_name'],FILTER_SANITIZE_STRING);
if($tile == '') {
	$tile = 'project';
}
$security = get_security($dbc, $tile); ?>
<script type="text/javascript">
function archiveProjectForm(a) {
	if(confirm('Are you sure you want to delete this Form?')) {
		var projectform = $(a).data('projectform');
		$.ajax({
			url: '../Project/projects_ajax.php?action=archive_project_form',
			method: 'POST',
			data: { projectform: projectform },
			success: function(response) {
				$(a).closest('tr').remove();
			}
		});
	}
}
</script>

<?php
$project_form_id = $_GET['project_form_id'];
$form_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `id` = '$project_form_id'"));
$project_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `project_form` WHERE `deleted` = 0 AND `project_form_id` = '$project_form_id' AND `projectid` = '".$_GET['edit']."' ORDER BY `today_date` DESC"),MYSQLI_ASSOC); ?>

<?php if($security['edit'] > 0) { ?>
	<div class="pull-right"><a href="<?= WEBSITE_URL ?>/Project/projects.php?fill_user_form=1&projectid=<?= $_GET['edit'] ?>&project_form_id=<?= $project_form_id ?>" class="btn brand-btn">Add <?= $form_config['subtab_name'] ?></a></div>
	<div class="clearfix"></div>
<?php } ?>

<?php if(count($project_forms) > 0) { ?>
	<div id="no-more-tables" class="gap-top">
		<table class="table table-bordered">
			<tr class="hide-titles-mob">
				<th>Form</th>
				<th>Date</th>
				<th>PDF</th>
				<?php if($security['edit'] > 0) { ?>
					<th>Function</th>
				<?php } ?>
			</tr>
			<?php foreach($project_forms as $project_form) { ?>
				<tr>
					<td data-title="Form"><?= $form_config['subtab_name'] ?></td>
					<td data-title="Date"><?= $project_form['today_date'] ?></td>
					<td data-title="PDF"><a href="<?= WEBSITE_URL ?>/Project/<?= $project_form['pdf_path'] ?>"><img src="<?= WEBSITE_URL ?>/img/pdf.png" class="inline-img"></a></td>
					<?php if($security['edit'] > 0) { ?>
						<td data-title="Function"><a href="<?= WEBSITE_URL ?>/Project/projects.php?fill_user_form=1&projectid=<?= $_GET['edit'] ?>&project_form_id=<?= $project_form_id ?>&projectform=<?= $project_form['id'] ?>">Edit</a> | <a href="" data-projectform="<?= $project_form['id'] ?>"  onclick="archiveProjectForm(this); return false;">Delete</a></td>
					<?php } ?>
				</tr>
			<?php } ?>
		</table>
	</div>
<?php } else {
	echo '<h3>No Forms Found.</h3>';
}