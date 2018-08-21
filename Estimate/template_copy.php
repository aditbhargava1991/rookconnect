<?php if($_POST['submit'] == 'create') {
	$estimateid = filter_var($_POST['src_estimate'],FILTER_SANITIZE_STRING);
	$template = filter_var($_POST['template_name'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `estimate_templates` (`template_name`) VALUES ('$template')");
	$templateid = mysqli_insert_id($dbc);
	$headings = mysqli_query($dbc, "SELECT `heading` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 GROUP BY `heading`");
	while($heading = mysqli_fetch_assoc($headings)['heading']) {
		mysqli_query($dbc, "INSERT INTO `estimate_template_headings` (`template_id`, `heading_name`) VALUES ('$templateid','$heading')");
		$before_change = '';
		$history = "Estimates template heading entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
		$headingid = mysqli_insert_id($dbc);
		mysqli_query($dbc, "INSERT INTO `estimate_template_lines` (`heading_id`, `src_table`, `description`, `src_id`, `qty`, `sort_order`) SELECT '$headingid', `src_table`, `description`, `src_id`, `qty`, `sort_order` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `heading`='$heading' AND `deleted`=0");
		$before_change = '';
		$history = "Estimates template line entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	}

	echo "<script> window.location.replace('?template=".$templateid."'); </script>";
} ?>
<form class="form-horizontal" method="POST" action="">
	<?php $estimate_list = mysqli_query($dbc, "SELECT `estimateid`, `estimate_name`, `created_date` FROM `estimate` WHERE `deleted`=0 AND `estimate_name` IS NOT NULL OR `created_date` IS NOT NULL"); ?>
	<div class="form-group">
		<label class="col-sm-4 control-label">Source <?= ESTIMATE_TILE ?>:</label>
		<div class="col-sm-8">
			<select class="chosen-select-deselect" data-placeholder="Select an Estimate" name="src_estimate">
				<option></option>
				<?php while($estimate_name = mysqli_fetch_assoc($estimate_list)) { ?>
					<option value="<?= $estimate_name['estimateid'] ?>"><?= $estimate_name['estimate_name'].' '.$estimate_name['created_date'] ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Template Name:</label>
		<div class="col-sm-8">
			<input class="form-control" name="template_name">
		</div>
	</div>
	<button class="btn brand-btn pull-right" type="submit" name="submit" value="create">Create Template</button>
</form>
