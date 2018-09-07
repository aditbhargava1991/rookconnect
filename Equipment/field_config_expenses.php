<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');
if(isset($_POST['expenses'])) {
	$equipment_expense_fields = implode(',', $_POST['equipment_expense_fields']);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_expense_fields' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_expense_fields') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$equipment_expense_fields' WHERE `name`='equipment_expense_fields'");
	
	echo "<script> window.location.replace('?settings=expenses') </script>";
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
	<script>
	$(document).ready(function() {
		$('.sortable').sortable({
		  connectWith: '.sortable',
		  items: 'label'
		});
	});
	</script>
	<style>
	.sortable label {
		background-color: RGBA(255,255,255,0.2);
		margin: 0.25em;
	}
	</style>
	<h4>Choose Fields for Expenses</h4>
	<div id='no-more-tables'>
		Move the fields around to change the display order.
		<div class='sortable' style='border:solid 1px black;'>
			<?php $equipment_expense_fields = explode(',',trim(get_config($dbc, 'equipment_expense_fields'),','));
			$equipment_expense_fields_arr = array_filter(array_unique(array_merge($equipment_expense_fields,explode(',','Description,Country,Province,Date,Receipt,Amount,HST,PST,GST,Total'))));
			foreach($equipment_expense_fields_arr as $field) {
				echo '<label class="form-checkbox"><input type="checkbox" '.(in_array($field,$equipment_expense_fields) ? 'checked' : '').' value="'.$field.'" name="equipment_expense_fields[]"> ';
				if($field == 'Description') {
					echo 'Description';
				} else if($field == 'Country') {
					echo 'Country of Expense';
				} else if($field == 'Province') {
					echo 'Province of Expense';
				} else if($field == 'Date') {
					echo 'Expense Date';
				} else if($field == 'Receipt') {
					echo 'Receipt';
				} else if($field == 'Amount') {
					echo 'Amount';
				} else if($field == 'HST') {
					echo 'HST';
				} else if($field == 'PST') {
					echo 'PST';
				} else if($field == 'GST') {
					echo 'GST';
				} else if($field == 'Total') {
					echo 'Total';
				}
				echo '</label>';
			}
			?>
		</div>
	</div>
	
	<hr>

	<div class="form-group pull-right">
		<a href="?category=Top" class="btn brand-btn">Back</a>
		<button	type="submit" name="expenses" value="expenses" class="btn brand-btn">Submit</button>
	</div>
</form>