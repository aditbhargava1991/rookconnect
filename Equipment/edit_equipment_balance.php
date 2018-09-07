<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if(isset($_POST['submit'])) {
	$equipmentid = $_GET['edit'];
	$purchase_date = filter_var($_POST['purchase_date'],FILTER_SANITIZE_STRING);
	$purchase_amt = filter_var($_POST['purchase_amt'],FILTER_SANITIZE_STRING);
	$purchase_km = filter_var($_POST['purchase_km'],FILTER_SANITIZE_STRING);
	$sale_date = filter_var($_POST['sale_date'],FILTER_SANITIZE_STRING);
	$sale_amt = filter_var($_POST['sale_amt'],FILTER_SANITIZE_STRING);
	$bill_of_sale = filter_var($_POST['bill_of_sale_current'],FILTER_SANITIZE_STRING);
	if($_FILES['bill_of_sale']['name'] != '') {
		$basefilename = $bill_of_sale = preg_replace('/[^A-Za-z0-9\.]/','_',$_FILES['bill_of_sale']['name']);
		$i = 0;
		while(file_exists('download/'.$bill_of_sale)) {
			$bill_of_sale = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', $basefilename);
		}
		move_uploaded_file($_FILES['bill_of_sale']['tmp_name'], "download/".$bill_of_sale);
	}

	$before_change = capture_before_change($dbc, 'equipment', 'purchase_date', 'equipmentid', $equipmentid);
	$before_change .= capture_before_change($dbc, 'equipment', 'purchase_amt', 'equipmentid', $equipmentid);
	$before_change .= capture_before_change($dbc, 'equipment', 'purchase_km', 'equipmentid', $equipmentid);
	$before_change .= capture_before_change($dbc, 'equipment', 'sale_date', 'equipmentid', $equipmentid);
	$before_change .= capture_before_change($dbc, 'equipment', 'sale_amt', 'equipmentid', $equipmentid);
	$before_change .= capture_before_change($dbc, 'equipment', 'bill_of_sale', 'equipmentid', $equipmentid);

	$sql = "UPDATE `equipment` SET `purchase_date`='$purchase_date', `purchase_amt`='$purchase_amt', `purchase_km`='$purchase_km', `sale_date`='$sale_date', `sale_amt`='$sale_amt', `bill_of_sale`='$bill_of_sale' WHERE `equipmentid`='$equipmentid'";
	mysqli_query($dbc, $sql);

	$history = capture_after_change('purchase_date', $purchase_date);
	$history .= capture_after_change('purchase_amt', $purchase_amt);
	$history .= capture_after_change('purchase_km', $purchase_km);
	$history .= capture_after_change('sale_date', $sale_date);
	$history .= capture_after_change('sale_amt', $sale_amt);
	$history .= capture_after_change('bill_of_sale', $bill_of_sale);

	add_update_history($dbc, 'equipment_history', $history, '', $before_change);
} ?>
<script type="text/javascript">
$(document).ready(function() {
	// Active tabs
	$('[data-tab-target]').click(function() {
		$('.main-screen .main-screen').scrollTop($('#tab_section_'+$(this).data('tab-target')).offset().top + $('.main-screen .main-screen').scrollTop() - $('.main-screen .main-screen').offset().top);
		return false;
	});
	setTimeout(function() {
		$('.main-screen .main-screen').scroll(function() {
			var screenTop = $('.main-screen .main-screen').offset().top + 10;
			var screenHeight = $('.main-screen .main-screen').innerHeight();
			$('.active.blue').removeClass('active blue');
			$('.tab-section').filter(function() { return $(this).offset().top + this.clientHeight > screenTop && $(this).offset().top < screenTop + screenHeight; }).each(function() {
				$('[data-tab-target='+$(this).attr('id').replace('tab_section_','')+']').find('li').addClass('active blue');
			});
		});
		$('.main-screen .main-screen').scroll();
	}, 500);
});
</script>
<?php if(!empty($_GET['archive']) && $_GET['archive'] == 'true') {
	$archiveid = $_GET['archiveid'];
    $date_of_archival = date('Y-m-d');
	mysqli_query($dbc, "UPDATE `equipment` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `equipmentid`='$archiveid'");
	echo "<script> window.location.replace('?tab=balance'); </script>";
}
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$equipmentid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
$get_equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT `equipment`.*, SUM(`equipment_expenses`.`total`) expense_total, `invoiced_hours`, `invoiced_amt`, 0 `invoiced_daily` FROM `equipment` LEFT JOIN (SELECT `item_id` `equipmentid`, SUM(`hours_estimated`) `invoiced_hours`, SUM(`hours_estimated` * `rate`) `invoiced_amt` FROM `ticket_attached` WHERE `src_table` LIKE 'equipment' AND `deleted`=0 GROUP BY `item_id`) `invoiced` ON `equipment`.`equipmentid`=`invoiced`.`equipmentid` LEFT JOIN `equipment_expenses` ON `equipment`.`equipmentid`=`equipment_expenses`.`equipmentid` AND `equipment_expenses`.`status` != 'Rejected' WHERE `equipment`.`equipmentid`='".$equipmentid."'"));

$unit_number = $get_equipment['unit_number'];
$category = $get_equipment['category'];
$make = $get_equipment['make'];
$model = $get_equipment['model'];
$purchase_date = $get_equipment['purchase_date'];
$purchase_amt = $get_equipment['purchase_amt'];
$purchase_km = $get_equipment['purchase_km'];
$sale_date = $get_equipment['sale_date'];
$sale_amt = $get_equipment['sale_amt'];
$bill_of_sale = $get_equipment['bill_of_sale'];
$invoiced_amt = $get_equipment['invoiced_amt'];
$expense_total = $get_equipment['expense_total'];
$profit_loss = $invoiced_hourly + $invoiced_daily + $sale_amt - $purchase_amt - $expense_total; ?>

<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
	<ul>
		<a href="?category=Top"><li>Back to Dashboard</li></a>
		<a href="" data-tab-target="equip"><li class="active blue">Equipment Details</li></a>
		<a href="" data-tab-target="purchase"><li>Purchase Information</li></a>
		<a href="" data-tab-target="sell"><li>Sell Information</li></a>
		<a href="" data-tab-target="expense"><li>Expense History</li></a>
		<a href="" data-tab-target="service"><li>Service History</li></a>
		<a href="" data-tab-target="financial"><li>Equipment Financial Summary</li></a>
	</ul>
</div>

<div class="scale-to-fill has-main-screen" style="overflow: hidden;">
	<div class="main-screen standard-body form-horizontal">
		<div class="standard-body-title">
 			<h3>Equipment Unit #<?= $unit_number ?>: Balance Sheet</h3>
		</div>

		<div class="standard-body-content" style="padding: 0.5em;">

			<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
				<div id="tab_section_equip" class="tab-section col-sm-12">
					<h4>Equipment Details</h4>
					<?php $value_config = ',Category,Make,Model,Unit #,';
					include('add_equipment_fields.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_purchase" class="tab-section col-sm-12">
					<h4>Purchase Information</h4>
					<?php $value_config = ',Purchase Date,Purchase Amount,Purchase KM,';
					include('add_equipment_financial.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_sell" class="tab-section col-sm-12">
					<h4>Sell Information</h4>
					<?php $value_config = ',Sale Date,Sale Amount,Bill of Sale,';
					include('add_equipment_financial.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_expense" class="tab-section col-sm-12">
					<h4>Expense History</h4>
					<?php $value_config = ',Expense History,';
					include('add_equipment_financial.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_service" class="tab-section col-sm-12">
					<h4>Service History</h4>
					<?php $value_config = ',Service History,';
					include('add_equipment_financial.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_financial" class="tab-section col-sm-12">
					<h4>Equipment Financial Summary</h4>
					<?php $value_config = ',View Purchase Amount,Invoiced Amt,Expenses,View Sale Amount,Profit Loss,';
					include('add_equipment_financial.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div class="form-group">
					<div class="col-sm-6">
						<p><span class="brand-color"><em>Required Fields *</em></span></p>
					</div>
					<div class="col-sm-6">
						<div class="pull-right">
							<a href="?category=Top" class="btn brand-btn">Back</a>
							<button	type="submit" name="submit"	value="Submit" class="btn brand-btn">Submit</button>
						</div>
					</div>
				</div>

			</form>
		</div>
	</div>
</div>
