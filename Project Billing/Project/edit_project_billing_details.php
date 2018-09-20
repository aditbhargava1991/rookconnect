<?php error_reporting(0);
include_once('../include.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project');
$invoiceid = filter_var($_GET['billing'],FILTER_SANITIZE_STRING);
$invoice = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid`='$invoiceid'")); ?>
<script>
$(document).ready(function() {
	$('.toggle-switch').click(function() {
		$(this).find('img').toggle();
		$(this).find('input').val($(this).find('input').val() == 'No' ? 'Yes' : 'No').change();
	});
});
</script>
<h3><?= $invoice['tile_name'] == 'field_service_ticket' ? 'Field Service Ticket' : ($invoice['tile_name'] == 'purchase_order' ? 'Purchase Order' : 'Invoice') ?> #<?= $invoiceid ?></h3>
<?php if($invoiceid > 0) { ?>
	<div class="dashboard-item">
		<h4><?= $invoice['tile_name'] == 'field_service_ticket' ? 'Field Service Ticket' : ($invoice['tile_name'] == 'purchase_order' ? 'Purchase Order' : 'Invoice') ?> #<?= $invoice['invoiceid'] ?>
			<div class="<?= $security['edit'] > 0 ? 'toggle-switch' : '' ?> form-group pull-right"><input type="hidden" name="paid" data-table="invoice" data-id-field="invoiceid" data-id="<?= $invoice['invoiceid'] ?>" value="<?= $invoice['paid'] ?>">Paid: 
				<img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" style="height: 2em; <?= $invoice['paid'] == 'Yes' ? 'display: none;' : '' ?>">
				<img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" style="height: 2em; <?= $invoice['paid'] == 'Yes' ? '' : 'display: none;' ?>"></div>
				<div class="clearfix"></div></h4>
		<div class="block-group col-sm-6">
			<div class="form-group">
				<?php $business = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='{$invoice['businessid']}'")); ?>
				<label class="col-sm-4"><?= $business['category'] ?>:</label>
				<div class="col-sm-8">
					<?php if($business['name'] != '') {
						echo decryptIt($business['name']);
					} else {
						echo decryptIt($business['first_name']).' '.decryptIt($business['last_name']);
					} ?>
				</div>
			</div>
			<div class="form-group">
				<?php $client = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='{$invoice['patientid']}'")); ?>
				<label class="col-sm-4"><?= $client['category'] ?>:</label>
				<div class="col-sm-8">
					<?php if($client['first_name'] != '') {
						echo decryptIt($client['first_name']).' '.decryptIt($client['last_name']);
					} else {
						echo decryptIt($client['name']);
					} ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4"><?= $client['category'] ?> #:</label>
				<div class="col-sm-8">
					<?php if($client['cell_phone'] != '') {
						echo decryptIt($client['cell_phone']);
					} else if($client['office_phone'] != '') {
						echo decryptIt($client['office_phone']);
					} else {
						echo decryptIt($client['home_phone']);
					} ?>
				</div>
			</div>
		</div>
		<div class="block-group col-sm-6">
			<div class="form-group">
				<?php $business = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='{$invoice['businessid']}'")); ?>
				<label class="col-sm-4">Total Due:</label>
				<div class="col-sm-8">
					$<?= number_format($invoice['final_price'],2) ?>
				</div>
			</div>
		</div>
		<div class="col-sm-12 block-group">
			<table class="table table-bordered">
				<tr>
					<th>Heading</th>
					<th>Description</th>
					<th>Qty</th>
					<th>Unit Price</th>
					<th>Line Total</th>
				</tr>
				<?php $lines = mysqli_query($dbc, "SELECT * FROM `invoice_lines` WHERE `invoiceid`='$invoiceid'");
				while($line = mysqli_fetch_array($lines)) { ?>
					<tr>
						<td><?= $line['heading'] ?></td>
						<td><?= $line['description'] ?></td>
						<td><?= $line['quantity'] ?></td>
						<td><?= $line['unit_price'] ?></td>
						<td><?= $line['sub_total'] ?></td>
					</tr>
				<?php } ?>
			</table>
		</div>
		<?php if(file_exists(WEBSITE_URL.'/Invoice/Download/invoice_'.$invoiceid.'.pdf')) { ?>
			<a href="<?= WEBSITE_URL ?>/Invoice/Download/invoice_<?= $invoice['invoiceid'] ?>.pdf" class="btn brand-btn pull-right">Export PDF</a>
		<?php } else { ?>
			<a href="<?= WEBSITE_URL ?>/Project/edit_project_billing_invoices_pdf.php?&invoiceid=<?= $invoice['invoiceid'] ?>" class="btn brand-btn pull-right" target="_blank">Export PDF</a>
		<?php } ?>
		<div class="clearfix"></div>
	</div>
<?php } else {
	echo "<h2>Invalid record selected.</h2>";
}
include('next_buttons.php'); ?>