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
$tab_label = $_GET['tab'] == 'field_service_ticket' ? 'Field Service Tickets' : ($_GET['tab'] == 'purchase_order' ? 'Purchase Orders' : ($_GET['tab'] == 'paid' ? 'Paid Invoices' : ($_GET['tab'] == 'outstanding' ? 'Outstanding Invoices' : ($_GET['tab'] == 'work_ticket' ? 'Work Tickets' : 'Invoices'))));
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project'); ?>
<script>
$(document).ready(function() {
	$('.toggle-switch').click(function() {
		$(this).find('img').toggle();
		$(this).find('input').val($(this).find('input').val() == 'No' ? 'Yes' : 'No').change();
	});
});
</script>
<!-- <h3><?= $tab_label ?></h3> -->
<?php $invoices = '';
if($_GET['tab'] == 'outstanding') {
	$invoices = "`paid`!='Yes' AND ";
} else if($_GET['tab'] == 'paid') {
	$invoices = "`paid`='Yes' AND ";
} else {
	$invoices = "`tile_name`='".filter_var($_GET['tab'],FILTER_SANITIZE_STRING)."' AND ";
}
$invoice_list = mysqli_query($dbc, "SELECT * FROM `invoice` WHERE $invoices `projectid`='$projectid' AND `deleted`=0 AND `status` NOT IN ('Void','Archived')");
if(mysqli_num_rows($invoice_list) > 0) {
	while($invoice = mysqli_fetch_array($invoice_list)) { ?>
		<div class="dashboard-item">
			<h4><a href="?edit=<?= $projectid ?>&tab=billing_details&billing=<?= $invoice['invoiceid'] ?>">Invoice #<?= $invoice['invoiceid'] ?></a>
				<div class="<?= $security['edit'] > 0 ? 'toggle-switch' : '' ?> form-group pull-right"><input type="hidden" name="paid" data-table="invoice" data-id-field="invoiceid" data-id="<?= $invoice['invoiceid'] ?>" value="<?= $invoice['paid'] ?>">Paid: 
					<img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" style="height: 2em; <?= $invoice['paid'] == 'Yes' ? 'display: none;' : '' ?>">
					<img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" style="height: 2em; <?= $invoice['paid'] == 'Yes' ? '' : 'display: none;' ?>"></div></h4>
			<div class="form-group col-sm-6">
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
			<div class="form-group col-sm-6">
				<label class="col-sm-4">Total Due:</label>
				<div class="col-sm-8">
					$<?= number_format($invoice['final_price'],2) ?>
				</div>
			</div>
			<div class="form-group col-sm-6">
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
			<div class="form-group col-sm-6">
				<label class="col-sm-4">Status:</label>
				<div class="col-sm-8">
					<select name="status" data-id-field="invoiceid" data-id="<?= $invoice['invoiceid'] ?>" data-table="invoice" data-placeholder="Select a status" class="chosen-select-deselect form-control">
						<option value=""></option>
						<option value="Sent to Customer" <?php if ($invoice['status'] == "Sent to Customer") { echo " selected"; } ?> >Sent to Customer</option>
						<option value="Posted" <?php if ($invoice['status'] == "Posted") { echo " selected"; } ?> >Posted</option>
						<option value="Posted Past Due" <?php if ($invoice['status'] == "Posted Past Due") { echo " selected"; } ?> >Posted Past Due</option>
						<option value="Completed" <?php if ($invoice['status'] == "Completed") { echo " selected"; } ?> >Completed</option>
						<option value="Void" <?php if ($invoice['status'] == "Void") { echo " selected"; } ?> >Void</option>
						<option value="Archived" <?php if ($invoice['status'] == "Archived") { echo " selected"; } ?> >Archive</option>
					</select>
				</div>
			</div>
			<?php if(file_exists(WEBSITE_URL.'/Invoice/Download/invoice_'.$invoiceid.'.pdf')) { ?>
				<a href="<?= WEBSITE_URL ?>/Invoice/Download/invoice_<?= $invoice['invoiceid'] ?>.pdf" class="btn brand-btn pull-right">Export PDF</a>
			<?php } else { ?>
				<a href="<?= WEBSITE_URL ?>/Project/edit_project_billing_invoices_pdf.php?invoiceid=<?= $invoice['invoiceid'] ?>" class="btn brand-btn pull-right" target="_blank">Export PDF</a>
			<?php } ?>
			<div class="clearfix"></div>
		</div>
	<?php }
} else {
	echo "<h2>No ".$tab_label." Found</h2>";
}
include('next_buttons.php'); ?>