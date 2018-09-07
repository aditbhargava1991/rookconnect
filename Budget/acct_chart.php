<?php $category = filter_var($_GET['cat'],FILTER_SANITIZE_STRING); ?>
<?php switch($_GET['type']) {
	case 'product':
		$vendor = $category;
		include('../Products/product_table.php');
		break;
	case 'material':
		$_GET['category'] = $category;
		include('../Material/materials_table.php');
		break;
	case 'inventory':
		$_GET['category'] = $category;
		include('../Inventory/inventory_table.php');
		break;
	case 'asset':
		$_GET['category'] = $category;
		include('../Asset/asset_table.php');
		break;
	case 'equip':
		$status = 'Active';
		unset($equipment);
		$_GET['category'] = $category;
		include('../Equipment/dashboard_equipment_list.php');
		break;
	case 'custom':
		$vendor = $category;
		include('../Custom/custom_table.php');
		break;
	case 'expense':
		include('../Expense/report_function.php');
		echo report_expense($dbc, '0000-00-00', '9999-01-01', $category, '','','');
		break;
} ?>