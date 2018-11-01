<?php include('config.php');
error_reporting(0);
if(!empty($_POST['submit'])) {
	$type = $_POST['type'];
	$priority = $_POST['priority'];
	$heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
	$image = filter_var($_POST['image'],FILTER_SANITIZE_STRING);
	$link = filter_var($_POST['link'],FILTER_SANITIZE_STRING);
	$description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);

	if(!empty($_GET['serviceid'])) {
		$query = "UPDATE `support_services` SET `type`='$type', `priority`='$priority', `heading`='$heading', `image`='$image', `link`='$link', `description`='$description' WHERE `serviceid`='".$_GET['serviceid']."'";
	} else {
		$query = "INSERT INTO `support_services` (`type`, `priority`, `heading`, `image`, `link`, `description`) VALUES ('$type', '$priority', '$heading', '$image', '$link', '$description')";
	}
	mysqli_query($dbc, $query);
	echo "<script> window.location.replace('customer_support.php?tab=services'); </script>";
} else if(!empty($_GET['delete'])) {
	$serviceid = $_GET['serviceid'];
    $date_of_archival = date('Y-m-d');
	mysqli_query($dbc, "UPDATE `support_services` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `serviceid`='$serviceid'");
	echo "<script> window.location.replace('customer_support.php?tab=services'); </script>";
} ?>
<script>
$(document).on('change', 'select[name="type"]', function() { reload_priorities(this.value); });
function reload_priorities(type) {
	$.ajax({
		url: 'support_ajax.php?fill=priorities',
		data: { type: type },
		method: 'POST',
		success: function(response) {
			$('[name=priority]').empty().html(response).trigger('change.select2');
		}
	});
}
</script>
</head>
<body>
<?php include_once ('../navigation.php');
$type = '';
$priority = '';
$heading = '';
$image = '';
$link = '';
$description = '';
if(!empty($_GET['serviceid'])) {
	$service = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `support_services` WHERE `serviceid`='".$_GET['serviceid']."'"));
	$type = $service['type'];
	$priority = $service['priority'];
	$heading = $service['heading'];
	$image = $service['image'];
	$link = $service['link'];
	$description = $service['description'];
}
$priorities = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`priority`) list, MAX(`priority`) high FROM `support_services` WHERE `type`='$type'"));
$priority_list = explode(',',$priorities['list']); ?>
<div class='container'>
	<div class='row'>
		<form class='form' method='POST' action=''>
			<h1>Add Entry to Support Services Tab</h1>
			<div class="panel-group" id="accordion2">
				<div class="panel panel-default hide_in_iframe">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_service" >
								Services Tab Entry<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<div id="collapse_service" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="form-group clearfix">
								<label class="col-sm-4">Type</label>
								<div class="col-sm-8">
									<select name="type" class="chosen-select-deselect form-control"><option></option>
										<option <?= ($type == 'service' ? 'selected' : '') ?> value="service">Service</option>
										<option <?= ($type == 'product' ? 'selected' : '') ?> value="product">Product</option>
										<option <?= ($type == 'plan' ? 'selected' : '') ?> value="plan">Service Plan</option></select>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-sm-4">Sort Order</label>
								<div class="col-sm-8">
									<select name="priority" class="chosen-select-deselect form-control"><option></option>
										<?php for($i = 0; $i <= $priorities['high']; $i++) {
											echo "<option ".($priority == ($i + 1) ? 'selected' : (in_array($i+1,$priority_list) ? 'disabled' : ''))." value='".($i+1)."'>".($i+1)."</option>";
										} ?>
										</select>
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-sm-4">Heading</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="heading" value="<?= $heading ?>" />
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-sm-4">Image<br /><em>Paste the link to an image that has already been uploaded.</em></label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="image" value="<?= $image ?>" />
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-sm-4">Link</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="link" value="<?= $link ?>" />
								</div>
							</div>
							<div class="form-group clearfix">
								<label class="col-sm-4">Description</label>
								<div class="col-sm-8">
									<textarea name="description"><?= html_entity_decode($description) ?></textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<a href="customer_support.php?tab=services" class='btn brand-btn pull-left'>Back</a>
			<button type='submit' name='submit' value='submit' class='btn brand-btn pull-right'>Submit</button>
		</form>
	</div>
</div>
<?php include('../footer.php'); ?>