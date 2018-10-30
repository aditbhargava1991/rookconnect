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
$wcb_invoice = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `patientformid` FROM `user_forms` LEFT JOIN `patientform_pdf` ON `user_forms`.`form_id`=`patientform_pdf`.`form_name` WHERE `user_forms`.`name`='WCB Invoice'"))['patientformid'];
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project'); ?>
<script>
$(document).ready(function() {
	set_save_fields();
});
function add_payment() {
	destroyInputs();
	var clone = $('.pay-group').last().clone();
	clone.data('id','').data('table','project_payments');
	clone.find('input').val('');
	clone.find('span').first().text('Payment '+($('.pay-group').length + 1));
	clone.find('span').first().next().html('<input type="text" name="heading" class="form-control">');
	clone.find('span').first().next().next().html('<input type="number" name="amount" class="form-control">');
	$('.pay-group').last().after(clone);
	set_save_fields();
	clone.find('input').first().focus();
}
function remove_payment(line) {
	var line = $(line).closest('.pay-group');
	line.find('[name=deleted]').val(1).change();
	line.find('[name=status]').val('Void');
	if($('.pay-group').length <= 1) {
		add_payment();
	}
	line.remove();
}
function set_save_fields() {
	$('input').off('change',saveField).change(saveField);
	initInputs();
}
function saveFieldMethod(field) {
	$.post('projects_ajax.php?action=payment_details',{
		id: $(field).closest('.pay-group').data('id'),
		table: $(field).closest('.pay-group').data('table'),
		field: field.name,
		value: field.value,
		projectid: '<?= $projectid ?>'
	},function(response) {
		if(response > 0) {
			$(field).closest('.pay-group').data('id',response);
		}
		doneSaving();
	});
}
</script>
<?php if(IFRAME_PAGE) { ?>
	<h3>Payment Schedule<a href="../blank_loading_page.php" class="pull-right"><img class="inline-img" src="../img/icons/cancel.png"></a></h3>
<?php } ?>
<?php $query = $dbc->query("SELECT * FROM (SELECT '' `heading`, `invoice`.`tile_name`, `invoice`.`invoiceid`, `invoice`.`invoiceid` `id`, 'invoice' `table`, `invoice`.`total_price`, `invoice`.`due_date`, `invoice_payment`.`date_paid` FROM `invoice` LEFT JOIN `invoice_payment` ON `invoice`.`invoiceid`=`invoice_payment`.`invoiceid` WHERE `invoice`.`projectid`='$projectid' AND `invoice`.`status` NOT IN ('Void','Archived') UNION SELECT `heading`, '' `tile_name`, 0 `invoiceid`, `id`, 'project_payments' `table`, `amount` `total_price`, `due_date`, `date_paid` FROM `project_payments` WHERE `deleted`=0 AND `projectid`='$projectid') `payments` ORDER BY `due_date`"); ?>
<div class="form-horizontal">
	<div class="hide-titles-mob text-center">
		<span class="col-sm-2"></span>
		<span class="col-sm-2">Heading</span>
		<span class="col-sm-2">Total</span>
		<span class="col-sm-2">Expected Payment Date</span>
		<span class="col-sm-2">Paid Date</span>
		<span class="col-sm-1">History</span>
		<span class="col-sm-1"></span>
		<div class="clearfix"></div>
	</div>
	<?php $pay_line = 1;
	$payment = $query->fetch_assoc();
	do { ?>
		<div class="pay-group form-group" data-table="<?= empty($payment['table']) ? 'project_payments' : $payment['table'] ?>" data-id="<?= $payment['id'] ?>">
			<span class="col-sm-2">Payment <?= $pay_line++ ?></span>
			<span class="col-sm-2"><span class="show-on-mob">Heading</span>
				<?php if($payment['invoiceid'] > 0) { ?>
					<?= ucwords($payment['tile_name']).' #'.$payment['invoiceid'] ?>
				<?php } else { ?>
					<input type="text" name="heading" class="form-control" value="<?= $payment['heading'] ?>">
				<?php } ?>
			</span>
			<span class="col-sm-2"><span class="show-on-mob">Total</span>
				<?php if($payment['invoiceid'] > 0) { ?>
					<?= number_format($payment['total_price'],2) ?>
				<?php } else { ?>
					<input type="number" min=0 step="any" name="amount" class="form-control" value="<?= $payment['total_price'] ?>">
				<?php } ?>
			</span>
			<span class="col-sm-2">
				<span class="show-on-mob">Expected Payment Date</span>
				<input type="text" name="due_date" value="<?= $payment['due_date'] ?>" class="form-control datepicker">
			</span>
			<span class="col-sm-2">
				<span class="show-on-mob">Paid Date</span>
				<input type="text" name="date_paid" value="<?= $payment['date_paid'] ?>" class="form-control datepicker">
			</span>
			<span class="col-sm-1 text-center"><?php if(!($payment['invoiceid'] > 0)) { ?><a href="../Project/payment_history.php?id=<?= $payment['id'] ?>" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><img class="inline-img" src="../img/icons/eyeball.png"></a><?php } ?></span>
			<span class="col-sm-1">
				<input type="hidden" name="deleted" value="0">
				<input type="hidden" name="status" value="">
				<img src="../img/remove.png" class="inline-img cursor-hand" onclick="remove_payment(this);">
				<img src="../img/icons/ROOK-add-icon.png" class="inline-img cursor-hand" onclick="add_payment();">
				<?php if($payment['invoiceid'] > 0) { ?>
					<!--<a href="../Invoice/create_invoice.php?invoiceid=<?= $payment['invoiceid'] ?>" onclick="overlayIFrameSlider(this.href,'auto',true,true);"><img src="../img/icons/ROOK-edit-icon.png" class="inline-img cursor-hand"></a>-->
				<?php } ?>
			</span>
		</div>
	<?php } while($payment = $query->fetch_assoc()); ?>
	<?php include('next_buttons.php'); ?>
</div>