<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if($_POST['equip_class_details'] == 'submit') {
	foreach($_POST['class'] as $i => $class_name) {
		$address['address'] = filter_var($_POST['address'][$i],FILTER_SANITIZE_STRING);
		$address['address2'] = filter_var($_POST['address2'][$i],FILTER_SANITIZE_STRING);
		$address['city'] = filter_var($_POST['city'][$i],FILTER_SANITIZE_STRING);
		$address['province'] = filter_var($_POST['province'][$i],FILTER_SANITIZE_STRING);
		$address['postal_code'] = filter_var($_POST['postal_code'][$i],FILTER_SANITIZE_STRING);
		$address['country'] = filter_var($_POST['country'][$i],FILTER_SANITIZE_STRING);
		set_config($dbc, 'equip_class_'.$class_name.'_address_start', json_encode($address));
	}
	echo '<script type="text/javascript"> window.location.replace("?settings=classification"); </script>';
}
?>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<?php
	$invtype = $_GET['tab'];
	$accr = $_GET['accr'];
	$type = $_GET['type'];

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config_equipment WHERE tab='$invtype' AND accordion='$accr'"));
	$equipment_config = ','.$get_field_config['equipment'].',';

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='$invtype' AND equipment_dashboard IS NOT NULL"));
	$equipment_dashboard_config = ','.$get_field_config['equipment_dashboard'].',';

	$get_field_order = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(`order` SEPARATOR ',') AS all_order FROM field_config_equipment WHERE tab='$invtype'"));
	?>

	<div class="panel-group" id="accordion2">
		<?php foreach(array_filter(array_unique(explode(',',get_config($dbc, '%_classification', true, ',')))) as $i => $classification) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_class_<?= $i ?>" >
							Equipment Classification Details: <?= $classification ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_class_<?= $i ?>" class="panel-collapse collapse">
					<div class="panel-body" id="no-more-tables">
						<h3>Default Start Address</h3>
						<?php $address = json_decode(html_entity_decode(get_config($dbc, 'equip_class_'.config_safe_str($classification).'_address_start')),true); ?>
						<input type="hidden" name="class[]" value="<?= config_safe_str($classification) ?>">
						<div class="form-group">
							<label class="col-sm-4 control-label">Address:</label>
							<div class="col-sm-8">
								<input type="text" name="address[]" value="<?= $address['address'] ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Address 2:</label>
							<div class="col-sm-8">
								<input type="text" name="address2[]" value="<?= $address['address2'] ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">City / Town:</label>
							<div class="col-sm-8">
								<input type="text" name="city[]" value="<?= $address['city'] ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Province:</label>
							<div class="col-sm-8">
								<input type="text" name="province[]" value="<?= $address['province'] ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Postal Code:</label>
							<div class="col-sm-8">
								<input type="text" name="postal_code[]" value="<?= $address['postal_code'] ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Country:</label>
							<div class="col-sm-8">
								<input type="text" name="country[]" value="<?= $address['country'] ?>" class="form-control">
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="form-group pull-right">
		<a href="?category=Top" class="btn brand-btn">Back</a>
		<button	type="submit" name="equip_class_details" value="submit" class="btn brand-btn">Submit</button>
	</div>
</form>