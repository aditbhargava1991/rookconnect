<?php
/*
Add	Inventory
*/
include ('../include.php');
error_reporting(0);
if(isset($_POST['submit'])) {
	$equipmentid = $_GET['equipmentid'];
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

	$sql = "UPDATE `equipment` SET `purchase_date`='$purchase_date', `purchase_amt`='$purchase_amt', `purchase_km`='$purchase_km', `sale_date`='$sale_date', `sale_amt`='$sale_amt', `bill_of_sale`='$bill_of_sale' WHERE `equipmentid`='$equipmentid'";
	mysqli_query($dbc, $sql);

	echo "<script> window.location.replace(''); </script>";
} ?>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('equipment');
if(!empty($_GET['archive']) && $_GET['archive'] == 'true') {
	$archiveid = $_GET['archiveid'];
    $date_of_archival = date('Y-m-d');
	mysqli_query($dbc, "UPDATE `equipment` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `equipmentid`='$archiveid'");
	echo "<script> window.location.replace('balance.php'); </script>";
}
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$equipmentid = filter_var($_GET['equipmentid'],FILTER_SANITIZE_STRING);
$get_equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT `equipment`.*, SUM(`equipment_expenses`.`total`) expense_total, 0 `invoiced_hourly`, 0 `invoiced_daily` FROM `equipment` LEFT JOIN `equipment_expenses` ON `equipment`.`equipmentid`=`equipment_expenses`.`equipmentid` AND `equipment_expenses`.`status` != 'Rejected' WHERE `equipment`.`equipmentid`='".$equipmentid."'"));

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
$invoiced_hourly = $get_equipment['invoiced_hourly'];
$invoiced_daily = $get_equipment['invoiced_daily'];
$expense_total = $get_equipment['expense_total'];
$profit_loss = $invoiced_hourly + $invoiced_daily + $sale_amt - $purchase_amt - $expense_total; ?>
<div class="container">
  <div class="row">

		<h1>Equipment Unit #<?= $unit_number ?>: Balance Sheet</h1>

		<div class="pad-left gap-top double-gap-bottom"><a href="equipment.php?category=<?php echo $category; ?>" class="btn brand-btn">Back to Dashboard</a></div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<div class="gap-left tab-container">
			<a href="add_equipment.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Equipment</a>
			<?php if ( in_array('Inspection',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'inspection') === TRUE ) { ?>
				<a href="equipment_inspections.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Inspections</a>
			<?php } ?>
			<?php if ( in_array('Work Order',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'work_order') === TRUE ) { ?>
				<a href="equipment_work_order.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Work Orders</a>
			<?php } ?>
			<?php if ( in_array('Schedules',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'schedules') === TRUE ) { ?>
				<a href="equipment_service.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Service Schedule</a>
			<?php } ?>
			<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
				<a href="equipment_expenses.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Expenses</a>
			<?php } ?>
			<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
				<a href="equipment_balance.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn active_tab">Balance Sheet</a>
			<?php } ?>
            <?php if ( in_array('Equipment Assignment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'eqipment', ROLE, 'equip_assign') === TRUE ) { ?>
                <a href="equipment_assignment.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Equipment Assignment</a>
            <?php } ?>
		</div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
			<div class="panel-group" id="accordion2">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_equip" >
								Equipment Details<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_equip" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $value_config = ',Category,Make,Model,Unit #,';
							include('add_equipment_fields.php'); ?>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_purchase" >
								Purchase Information<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_purchase" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $value_config = ',Purchase Date,Purchase Amount,Purchase KM,';
							include('add_equipment_financial.php'); ?>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_sell" >
								Sell Information<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_sell" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $value_config = ',Sale Date,Sale Amount,Bill of Sale,';
							include('add_equipment_financial.php'); ?>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_expense" >
								Expense History<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_expense" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $value_config = ',Expense History,';
							include('add_equipment_financial.php'); ?>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_service" >
								Service History<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_service" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $value_config = ',Service History,';
							include('add_equipment_financial.php'); ?>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_financial" >
								Equipment Financial Summary<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_financial" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $value_config = ',View Purchase Amount,Invoiced Hourly,Invoiced Daily,Expenses,View Sale Amount,Profit Loss,';
							include('add_equipment_financial.php'); ?>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-6">
					<a href="equipment.php?category=<?php echo $category; ?>"	class="btn brand-btn btn-lg">Back</a>
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
				</div>
			</div>
		</form>
	</div>
</div>

<?php include('../footer.php'); ?>