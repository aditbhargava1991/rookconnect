<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('equipment');
error_reporting(0);
$equipmentid = $_GET['equipmentid'];
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
$profit_loss = $invoiced_hourly + $invoiced_daily + $sale_amt - $purchase_amt - $expense_total;
?>
<script type="text/javascript">

</script>


</head>
<body style="min-height:0px;">

<div class="container">
	<div class="row">
		<h1>Balance Sheet Summary: Unit #<?= $get_equipment['unit_number'] ?></h1>
		<?php $value_config = ',View Purchase Amount,Invoiced Hourly,Invoiced Daily,Expenses,View Sale Amount,Profit Loss,';
		include('add_equipment_financial.php'); ?>
	</div>
</div>