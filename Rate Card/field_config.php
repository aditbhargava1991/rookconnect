<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('rate_card');
error_reporting(0);

if (isset($_POST['submit'])) {
	if($_POST['submit'] == 'tabs') {
		$tab_config = ','.implode(',',$_POST['tab_list']).',';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) tab_config FROM `general_configuration` WHERE `name`='rate_card_tabs'"));
		if($get_config['tab_config'] > 0) {
			$tab_sql = "UPDATE `general_configuration` SET `value`='$tab_config' WHERE `name`='rate_card_tabs'";
		} else {
			$tab_sql = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('rate_card_tabs','$tab_config')";
		}
		$result_tabs = mysqli_query($dbc, $tab_sql);
	} else if($_POST['submit'] == 'rate_types') {
		$rate_card_types = filter_var($_POST['rate_card_types'],FILTER_SANITIZE_STRING);
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(`configid`) AS configid FROM `general_configuration` WHERE `name`='rate_card_types'"));
		if($get_config['configid'] > 0) {
			$query_update_employee = "UPDATE `general_configuration` SET `value` = '$rate_card_types' WHERE `name`='rate_card_types'";
			$result_update_employee = mysqli_query($dbc, $query_update_employee);
		} else {
			$query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('rate_card_types', '$rate_card_types')";
			$result_insert_config = mysqli_query($dbc, $query_insert_config);
		}
	} else if($_POST['submit'] == 'company' || $_POST['submit'] == 'universal') {
		//Field Labels and Order
		// $labels = [];
		// foreach($_POST['estimate_field_name'] as $key => $name) {
			// $labels[] = $name.'***'.$_POST['estimate_field_label'][$key];
		// }
		// $label_config = implode('#*#',$labels);
		// $result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimate_field_order' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimate_field_order') num WHERE num.rows=0");
		// $result = mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$label_config' WHERE `name`='estimate_field_order'");
	}
	if($_POST['submit'] == 'company') {
		$field_config = ','.implode(',',$_POST['company_fields']).',';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) config FROM `general_configuration` WHERE `name`='company_rate_fields'"));
		if($get_config['config'] > 0) {
			$field_sql = "UPDATE `general_configuration` SET `value`='$field_config' WHERE `name`='company_rate_fields'";
		} else {
			$field_sql = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('company_rate_fields','$field_config')";
		}
		$result = mysqli_query($dbc, $field_sql);

		set_config($dbc,'estimate_field_order',implode('#*#',$_POST['field_sort']));
		set_config($dbc,'company_rate_card_sections',','.implode(',',$_POST['company_sections']).',');
		$db_config = ','.implode(',',$_POST['company_db']).',';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) config FROM `general_configuration` WHERE `name`='company_db_rate_fields'"));
		if($get_config['config'] > 0) {
			$db_sql = "UPDATE `general_configuration` SET `value`='$db_config' WHERE `name`='company_db_rate_fields'";
		} else {
			$db_sql = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('company_db_rate_fields', '$db_config')";
		}
		$result = mysqli_query($dbc, $db_sql);
	}
	elseif($_POST['submit'] == 'universal') {
		$field_config = ','.implode(',',$_POST['company_fields']).',';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) config FROM `general_configuration` WHERE `name`='universal_rate_fields'"));
		if($get_config['config'] > 0) {
			$field_sql = "UPDATE `general_configuration` SET `value`='$field_config' WHERE `name`='universal_rate_fields'";
		} else {
			$field_sql = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('universal_rate_fields','$field_config')";
		}
		$result = mysqli_query($dbc, $field_sql);
	}
	elseif($_POST['submit'] == 'customer') {

		$contact_categories = implode(',',$_POST['customer_contact_category']);
		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(*) AS num_rows FROM `general_configuration` WHERE `name` = 'customer_rate_card_contact_categories'"));
		if($get_field_config['num_rows'] > 0) {
			$query_update_employee = "UPDATE `general_configuration` SET `value` = '$contact_categories' WHERE `name` = 'customer_rate_card_contact_categories'";
			$result_update_employee = mysqli_query($dbc, $query_update_employee);
		} else {
			$query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('customer_rate_card_contact_categories', '$contact_categories')";
			$result_insert_config = mysqli_query($dbc, $query_insert_config);
		}

		$config_fields = implode(',',$_POST['config_fields']);

		if (strpos(','.$config_fields.',',','.'Package Heading,Promotion Heading,Custom Heading,Material Code,Services Heading,Products Heading,SRED Heading,Staff Contact Person,Contractor Contact Person,Clients Client Name,Clients Contact Person,Vendor Pricelist Vendor,Vendor Pricelist Price List,Vendor Pricelist Category,Vendor Pricelist Product,Customer Customer Name,Customer Contact Person,Inventory Product Name,Equipment Unit/Serial Number,Labour Heading,Expenses Type,Expenses Category,Other Detail'.',') === false) {
			$config_fields = 'Package Heading,Promotion Heading,Custom Heading,Material Code,Services Heading,Products Heading,SRED Heading,Staff Contact Person,Contractor Contact Person,Clients Client Name,Clients Contact Person,Vendor Pricelist Vendor,Vendor Pricelist Price List,Vendor Pricelist Category,Vendor Pricelist Product,Customer Customer Name,Customer Contact Person,Inventory Product Name,Equipment Unit/Serial Number,Labour Heading,Expenses Type,Expenses Category,Other Detail,'.$config_fields;
		}

		$rcdb_config = implode(',',$_POST['rcdb_config']);

		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(`fieldconfigratecardid`) AS fieldconfigratecardid FROM `field_config_ratecard`"));
		if($get_field_config['fieldconfigratecardid'] > 0) {
			$query_update_employee = "UPDATE `field_config_ratecard` SET `config_fields` = '$config_fields', `dashboard_fields` = '$rcdb_config' WHERE `fieldconfigratecardid` = 1";
			$result_update_employee = mysqli_query($dbc, $query_update_employee);
		} else {
			$query_insert_config = "INSERT INTO `field_config_ratecard` (`config_fields`, `dashboard_fields`) VALUES ('$config_fields', '$rcdb_config')";
			$result_insert_config = mysqli_query($dbc, $query_insert_config);
		}
	}
	elseif($_POST['submit'] == 'position') {
		$field_config = ','.implode(',',$_POST['field_config']).',';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) config FROM `general_configuration` WHERE `name`='position_rate_fields'"));
		if($get_config['config'] > 0) {
			$field_sql = "UPDATE `general_configuration` SET `value`='$field_config' WHERE `name`='position_rate_fields'";
		} else {
			$field_sql = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('position_rate_fields','$field_config')";
		}
		$result = mysqli_query($dbc, $field_sql);

		$db_config = ','.implode(',',$_POST['db_config']).',';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) config FROM `general_configuration` WHERE `name`='pos_db_rate_fields'"));
		if($get_config['config'] > 0) {
			$db_sql = "UPDATE `general_configuration` SET `value`='$db_config' WHERE `name`='pos_db_rate_fields'";
		} else {
			$db_sql = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('pos_db_rate_fields', '$db_config')";
		}
		$result = mysqli_query($dbc, $db_sql);
	}
	elseif($_POST['submit'] == 'staff') {
		$field_config = ','.implode(',',$_POST['field_config']).',';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) config FROM `general_configuration` WHERE `name`='staff_rate_fields'"));
		if($get_config['config'] > 0) {
			$field_sql = "UPDATE `general_configuration` SET `value`='$field_config' WHERE `name`='staff_rate_fields'";
		} else {
			$field_sql = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('staff_rate_fields','$field_config')";
		}
		$result = mysqli_query($dbc, $field_sql);

		$db_config = ','.implode(',',$_POST['db_config']).',';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) config FROM `general_configuration` WHERE `name`='staff_db_rate_fields'"));
		if($get_config['config'] > 0) {
			$db_sql = "UPDATE `general_configuration` SET `value`='$db_config' WHERE `name`='staff_db_rate_fields'";
		} else {
			$db_sql = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('staff_db_rate_fields', '$db_config')";
		}
		$result = mysqli_query($dbc, $db_sql);
	}
	elseif($_POST['submit'] == 'equipment') {
		$field_config = ','.implode(',',$_POST['field_config']).',';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) config FROM `general_configuration` WHERE `name`='equipment_rate_fields'"));
		if($get_config['config'] > 0) {
			$field_sql = "UPDATE `general_configuration` SET `value`='$field_config' WHERE `name`='equipment_rate_fields'";
		} else {
			$field_sql = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('equipment_rate_fields','$field_config')";
		}
		$result = mysqli_query($dbc, $field_sql);

		$db_config = ','.implode(',',$_POST['db_config']).',';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) config FROM `general_configuration` WHERE `name`='equip_db_rate_fields'"));
		if($get_config['config'] > 0) {
			$db_sql = "UPDATE `general_configuration` SET `value`='$db_config' WHERE `name`='equip_db_rate_fields'";
		} else {
			$db_sql = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('equip_db_rate_fields', '$db_config')";
		}
		$result = mysqli_query($dbc, $db_sql);
	}
	elseif($_POST['submit'] == 'category') {
		$field_config = ','.implode(',',$_POST['field_config']).',';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) config FROM `general_configuration` WHERE `name`='category_rate_fields'"));
		if($get_config['config'] > 0) {
			$field_sql = "UPDATE `general_configuration` SET `value`='$field_config' WHERE `name`='category_rate_fields'";
		} else {
			$field_sql = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('category_rate_fields','$field_config')";
		}
		$result = mysqli_query($dbc, $field_sql);

		$db_config = ','.implode(',',$_POST['db_config']).',';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) config FROM `general_configuration` WHERE `name`='cat_db_rate_fields'"));
		if($get_config['config'] > 0) {
			$db_sql = "UPDATE `general_configuration` SET `value`='$db_config' WHERE `name`='cat_db_rate_fields'";
		} else {
			$db_sql = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('cat_db_rate_fields', '$db_config')";
		}
		$result = mysqli_query($dbc, $db_sql);
	}
	elseif($_POST['submit'] == 'labour') {
		$db_config = ',labour_type,heading,start_date,end_date,price,'.implode(',',$_POST['db_config']).',';
		set_config($dbc, 'labour_db_rate_fields', $db_config);

		$field_config = ',labour_type,heading,start_date,end_date,price,'.implode(',',$_POST['field_config']).',';
		set_config($dbc, 'labour_rate_fields', $field_config);
	}
	elseif($_POST['submit'] == 'services') {
		$db_config = ','.implode(',',$_POST['db_config']).',';
		set_config($dbc, 'services_db_rate_fields', $db_config);

		$field_config = ','.implode(',',$_POST['field_config']).',';
		set_config($dbc, 'services_rate_fields', $field_config);
	}
	elseif($_POST['submit'] == 'holiday') {
		$db_config = ','.implode(',',$_POST['db_config']).',';
		set_config($dbc, 'holiday_db_rate_fields', $db_config);

		$field_config = ','.implode(',',$_POST['field_config']).',';
		set_config($dbc, 'holiday_rate_fields', $field_config);
	}
	elseif($_POST['submit'] == 'expense') {
		$db_config = ','.implode(',',$_POST['db_config']).',';
		set_config($dbc, 'expense_db_rate_fields', $db_config);

		$field_config = ','.implode(',',$_POST['field_config']).',';
		set_config($dbc, 'expense_rate_fields', $field_config);
	}
	echo '<script type="text/javascript"></script>';
}

$tab = isset($_GET['settings']) ? $_GET['settings'] : '';
switch($tab) {
	case 'universal': $tab = 'universal'; break;
	case 'position': $tab = 'position'; break;
	case 'staff': $tab = 'staff'; break;
	case 'category': $tab = 'category'; break;
	case 'equipment': $tab = 'equipment'; break;
	case 'customer': $tab = 'customer'; break;
	case 'company': $tab = 'company'; break;
	case 'rate_types': $tab = 'rate_types'; break;
	case 'field_sort': $tab = 'field_sort'; break;
	case 'labour': $tab = 'labour'; break;
	case 'services': $tab = 'services'; break;
	case 'holiday': $tab = 'holiday'; break;
	case 'expense': $tab = 'expense'; break;
	default: $tab = 'tabs'; break;
}

// Get the Rate Card Tabs config
$tab_config = get_config($dbc, 'rate_card_tabs');
switch($tab) {
	case 'universal':
		$title = "Universal Rates";
		$field_config = get_config($dbc, 'universal_rate_fields');
		break;
	case 'position':
		$title = "Position";
		$db_config = get_config($dbc, 'pos_db_rate_fields');
		$field_config = get_config($dbc, 'position_rate_fields');
		break;
	case 'staff':
		$title = "Staff";
		$db_config = get_config($dbc, 'staff_db_rate_fields');
		$field_config = get_config($dbc, 'staff_rate_fields');
		break;
	case 'equipment':
		$title = "Equipment";
		$db_config = get_config($dbc, 'equip_db_rate_fields');
		$field_config = get_config($dbc, 'equipment_rate_fields');
		break;
	case 'category':
		$title = "Equipment Category";
		$db_config = get_config($dbc, 'cat_db_rate_fields');
		$field_config = get_config($dbc, 'category_rate_fields');
		break;
	case 'customer':
		$title = "Customer";
		$db_config = "";
		$field_config = "";
		break;
	case 'company':
		$title = "Company";
		$db_config = get_config($dbc, 'company_db_rate_fields');
		$field_config = get_config($dbc, 'company_rate_fields');
		break;
	case 'rate_types':
		$title = "Rate Card Types";
		break;
	case 'field_sort':
		$title = "Field Display Order and Labels";
		break;
	case 'labour':
		$title = "Labour";
		$db_config = get_config($dbc, 'labour_db_rate_fields');
		$field_config = get_config($dbc, 'labour_rate_fields');
		break;
	case 'services':
		$title = "Services";
		$db_config = get_config($dbc, 'services_db_rate_fields');
		$field_config = get_config($dbc, 'services_rate_fields');
		break;
	case 'holiday':
		$title = "Holiday Pay";
		$db_config = get_config($dbc, 'holiday_db_rate_fields');
		$field_config = get_config($dbc, 'holiday_rate_fields');
		break;
	case 'expense':
		$title = "Expense Rates";
		$db_config = get_config($dbc, 'expense_db_rate_fields');
		$field_config = get_config($dbc, 'expense_rate_fields');
		break;
	default:
		$title = "Available Tabs";
		break;
}
if(str_replace(',','',$db_config) == '') {
	if($tab == 'company') {
		$db_config = ',card,total_cost,';
	}
	else {
		$db_config = ",card,annual,history,function,";
	}
}
if(str_replace(',','',$field_config) == '') {
	if($tab == 'company' || $tab == 'universal') {
		$field_config = ',tile,type,heading,description,uom,cost,estimate,customer,quantity,total,profit,margin,';
	}
	else {
		$field_config = ",annual,monthly,hourly,";
	}
}
?>
<script>
$(document).ready(function(){
	$("#selectall").change(function(){
	  $("input[name='config_fields[]']").prop('checked', $(this).prop("checked"));
	});
	$('[name="estimate_field_name[]"]').change(function() {
		if(this.checked) {
			$(this).closest('label').find('input[type=text]').removeAttr('disabled');
		} else {
			$(this).closest('label').find('input[type=text]').attr('disabled','disabled');
		}
	});
});
</script>
<div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible">
	<ul>
		<li class="<?php echo ($tab == 'tabs' ? ' active blue' : ''); ?>"><a href="?settings=tabs">Available Tabs</a></li>
		<li class="<?php echo ($tab == 'rate_types' ? ' active blue' : ''); ?>"><a href="?settings=rate_types">Rate Card Types</a></li>
		<li class="<?php echo ($tab == 'field_sort' ? ' active blue' : ''); ?>"><a href="?settings=field_sort">Field Display Order</a></li>
		<li class="<?php echo ($tab == 'customer' ? ' active blue' : ''); ?>"><a href="?settings=customer">Customer</a></li>
		<li class="<?php echo ($tab == 'company' ? ' active blue' : ''); ?>"><a href="?settings=company">Company</a></li>
		<li class="<?php echo ($tab == 'universal' ? ' active blue' : ''); ?>"><a href="?settings=universal">Universal Rates</a></li>
		<li class="<?php echo ($tab == 'position' ? ' active blue' : ''); ?>"><a href="?settings=position">Position</a></li>
		<li class="<?php echo ($tab == 'staff' ? ' active blue' : ''); ?>"><a href="?settings=staff">Staff</a></li>
		<li class="<?php echo ($tab == 'equipment' ? ' active blue' : ''); ?>"><a href="?settings=equipment">Equipment</a></li>
		<li class="<?php echo ($tab == 'category' ? ' active blue' : ''); ?>"><a href="?settings=category">Equipment Category</a></li>
		<li class="<?php echo ($tab == 'services' ? ' active blue' : ''); ?>"><a href="?settings=services">Services</a></li>
		<li class="<?php echo ($tab == 'labour' ? ' active blue' : ''); ?>"><a href="?settings=labour">Labour</a></li>
		<li class="<?php echo ($tab == 'holiday' ? ' active blue' : ''); ?>"><a href="?settings=holiday">Holiday Pay</a></li>
		<li class="<?php echo ($tab == 'expense' ? ' active blue' : ''); ?>"><a href="?settings=expense">Expenses</a></li>
	</ul>
</div>
<form id="form1" name="form1" method="post"	action="ratecards.php?settings=<?= $tab ?>" enctype="multipart/form-data" class="form-horizontal" role="form">
<div class='main-content-screen scale-to-fill has-main-screen hide-titles-mob'>
	<div class='main-screen standard-dashboard-body override-main-screen form-horizontal'>
		<div class="standard-dashboard-body-title"><h3><?= $title ?></h3></div>
			<?php if($tab == 'tabs') { ?>
				<h4 class="pad-10">Select the tabs that should be available on the main screen:</h4>
				<label class="form-checkbox"><input type="checkbox" <?= strpos($tab_config,',universal,') !== false ? "checked" : "" ?> value="universal" name="tab_list[]">Universal Rates</label>
				<label class="form-checkbox"><input type="checkbox" <?= strpos($tab_config,',company,') !== false ? "checked" : "" ?> value="company" name="tab_list[]">Company</label>
				<label class="form-checkbox"><input type="checkbox" <?= strpos($tab_config,',customer,') !== false ? "checked" : "" ?> value="customer" name="tab_list[]">Customer</label>
				<label class="form-checkbox"><input type="checkbox" <?= strpos($tab_config,',estimate,') !== false ? "checked" : "" ?> value="estimate" name="tab_list[]">Estimate Scope Template</label>
				<label class="form-checkbox"><input type="checkbox" <?= strpos($tab_config,',position,') !== false ? "checked" : "" ?> value="position" name="tab_list[]">Position</label>
				<label class="form-checkbox"><input type="checkbox" <?= strpos($tab_config,',staff,') !== false ? "checked" : "" ?> value="staff" name="tab_list[]">Staff</label>
				<label class="form-checkbox"><input type="checkbox" <?= strpos($tab_config,',equipment,') !== false ? "checked" : "" ?> value="equipment" name="tab_list[]">Equipment</label>
				<label class="form-checkbox"><input type="checkbox" <?= strpos($tab_config,',category,') !== false ? "checked" : "" ?> value="category" name="tab_list[]">Equipment Category</label>
				<label class="form-checkbox"><input type="checkbox" <?= strpos($tab_config,',services,') !== false ? "checked" : "" ?> value="services" name="tab_list[]">Services</label>
				<label class="form-checkbox"><input type="checkbox" <?= strpos($tab_config,',labour,') !== false ? "checked" : "" ?> value="labour" name="tab_list[]">Labour</label>
				<label class="form-checkbox"><input type="checkbox" <?= strpos($tab_config,',holiday,') !== false ? "checked" : "" ?> value="holiday" name="tab_list[]">Holiday Pay</label>
				<label class="form-checkbox"><input type="checkbox" <?= strpos($tab_config,',expense,') !== false ? "checked" : "" ?> value="expense" name="tab_list[]">Expenses</label>

			<?php } else if($tab == 'rate_types') { ?>
				<div class="form-group">
					<label for="fax_number"	class="col-sm-4	control-label">Add Types Separated By a Comma:<br /><small><em>These are used for Universal and Company Rate Cards.</em></small></label>
					<div class="col-sm-8">
						<input name="rate_card_types" type="text" value="<?php echo get_config($dbc, 'rate_card_types'); ?>" class="form-control"/>
					</div>
				</div>
			<?php } else if($tab == 'field_sort') { ?>
				These settings will affect the Rate Card, Estimates, and Projects. Move the fields around to change the display order.
				<div class='sortable' style='border:solid 1px black;'>
					<style>
					.sortable label {
						background-color: RGBA(255,255,255,0.2);
						margin: 0.5em;
						padding: 0.5em;
					}
					</style>
					<script>
					$(document).ready(function() {
						$('.sortable').sortable({
						  connectWith: '.sortable',
						  items: 'label'
						});
					});
					</script>
					<?php $estimate_field_order = get_config($dbc, 'estimate_field_order');
					if($estimate_field_order == '') {
						$estimate_field_order = $accordions[0];
						if($estimate_field_order == '') {
							$estimate_field_order = trim(str_replace([',itemtype,',',estimate,',',category,',',tile,',',breakdown,'], [',Item Type,',',Estimate Price,','','',''], get_config($dbc, 'company_rate_fields')),',');
							$estimate_field_order = explode(',', $estimate_field_order);
						} else {
							$estimate_field_order = explode(',', $estimate_field_order);
							unset($estimate_field_order[0]);
						}
					} else {
						$estimate_field_order = explode('#*#', $estimate_field_order);
					}
					$estimate_field_order = array_map('ucwords',$estimate_field_order);
					$defaults = 'Type#*#Heading#*#Category***Category (If Applicable)#*#Description#*#Detail#*#Billing Frequency#*#Item Type#*#Daily#*#Hourly#*#Customer Price#*#Dollarsaving#*#Percentsaving#*#UOM#*#Quantity#*#Cost#*#Margin#*#Profit#*#Estimate Price#*#Total';
					foreach($estimate_field_order as $value) {
						$defaults = trim(str_replace(','.explode('***',$value)[0].',',',',','.$defaults.','),',');
					}
					$estimate_field_arr = array_filter(array_unique(array_merge($estimate_field_order,explode(',',$defaults))));
					foreach($estimate_field_arr as $field_order) {
						$data = explode('***', $field_order);
						echo '<label class="form-checkbox"><input type="checkbox" '.(in_array($field_order,$estimate_field_order) ? 'checked' : '').' value="'.$data[0].'" name="estimate_field_name[]">';
						echo $data[0].': <input type="text" '.(in_array($field_order,$estimate_field_order) ? '' : 'disabled').' class="form-control" name="estimate_field_label[]" value="'.$data[1].'"></label>';
					} ?>
				</div>
			<?php } else if($tab == 'universal') { ?>
				<h4>The following fields will be shown or hidden within the Universal rate cards:</h4>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',category,') !== FALSE ? "checked" : "" ?> type="checkbox" value="category"/>Rate Card Category</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',tile,') !== FALSE ? "checked" : "" ?> type="checkbox" value="tile"/>Tile Name</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',type,') !== FALSE ? "checked" : "" ?> type="checkbox" value="type"/>Type</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',heading,') !== FALSE ? "checked" : "" ?> type="checkbox" value="heading"/>Heading</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',description,') !== FALSE ? "checked" : "" ?> type="checkbox" value="description"/>Description</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',itemtype,') !== FALSE ? "checked" : "" ?> type="checkbox" value="itemtype"/>Item Type</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',daily,') !== FALSE ? "checked" : "" ?> type="checkbox" value="daily"/>Daily Rate</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',hourly,') !== FALSE ? "checked" : "" ?> type="checkbox" value="hourly"/>Hourly Rate</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',uom,') !== FALSE ? "checked" : "" ?> type="checkbox" value="uom"/>UOM</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',cost,') !== FALSE ? "checked" : "" ?> type="checkbox" value="cost"/>Cost</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',estimate,') !== FALSE ? "checked" : "" ?> type="checkbox" value="estimate"/>Estimate Price</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',customer,') !== FALSE ? "checked" : "" ?> type="checkbox" value="customer"/>Customer Price</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',quantity,') !== FALSE ? "checked" : "" ?> type="checkbox" value="quantity"/>Quantity</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',total,') !== FALSE ? "checked" : "" ?> type="checkbox" value="total"/>Total</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',profit,') !== FALSE ? "checked" : "" ?> type="checkbox" value="profit"/>Profit</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',margin,') !== FALSE ? "checked" : "" ?> type="checkbox" value="margin"/>Margin</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',sort_order,') !== FALSE ? "checked" : "" ?> type="checkbox" value="sort_order"/>Sort Order</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',breakdown,') !== FALSE ? "checked" : "" ?> type="checkbox" value="breakdown"/>Breakdown</label>
			<?php } else if($tab == 'company') { ?>
				<h4>The following fields will be shown or hidden on the Company rate card dashboard:</h4>
				<label class="form-checkbox"><input name="company_db[]" <?php if (strpos($db_config, ',card,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="card"/>Rate Card Name</label>
				<label class="form-checkbox"><input name="company_db[]" <?php if (strpos($db_config, ',category,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="category"/>Rate Card Category</label>
				<label class="form-checkbox"><input name="company_db[]" <?php if (strpos($db_config, ',start_end_dates,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="start_end_dates"/>Start &amp; End Dates</label>
				<label class="form-checkbox"><input name="company_db[]" <?php if (strpos($db_config, ',alert_date,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="alert_date"/>Alert Date</label>
				<label class="form-checkbox"><input name="company_db[]" <?php if (strpos($db_config, ',alert_staff,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="alert_staff"/>Alert Staff</label>
				<label class="form-checkbox"><input name="company_db[]" <?php if (strpos($db_config, ',total_cost,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="total_cost"/>Total Cost</label>
				<h4>Select the Sections to display:</h4>
				<?php $company_sections = ','.get_config($dbc,'company_rate_card_sections').',';
				if(tile_enabled($dbc, 'tasks')['user_enabled'] || tile_enabled($dbc,'tickets')['user_enabled']) { ?>
					<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',tasks,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="tasks"/><?= TASK_TILE ?></label>
				<?php } ?>
				<?php if(tile_enabled($dbc, 'material')['user_enabled']) { ?>
					<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',material,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="material"/>Material</label>
				<?php } ?>
				<?php if(tile_enabled($dbc, 'services')['user_enabled']) { ?>
					<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',services,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="services"/>Services</label>
				<?php } ?>
				<?php if(tile_enabled($dbc, 'products')['user_enabled']) { ?>
					<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',products,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="products"/>Products</label>
				<?php } ?>
				<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',staff,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="staff"/>Staff</label>
				<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',position,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="position"/>Position</label>
				<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',contractor,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="contractor"/>Contractor</label>
				<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',clients,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="clients"/>Clients</label>
				<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',customer,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="customer"/>Customer</label>
				<?php if(tile_enabled($dbc, 'vpl')['user_enabled']) { ?>
					<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',vpl,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="vpl"/>Vendor Pricelist</label>
				<?php } ?>
				<?php if(tile_enabled($dbc, 'inventory')['user_enabled']) { ?>
					<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',inventory,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="inventory"/>Inventory</label>
				<?php } ?>
				<?php if(tile_enabled($dbc, 'equipment')['user_enabled']) { ?>
					<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',equipment,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="equipment"/>Equipment</label>
				<?php } ?>
				<?php if(tile_enabled($dbc, 'labour')['user_enabled']) { ?>
					<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',labour,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="labour"/>Labour</label>
				<?php } ?>
				<?php if(tile_enabled($dbc, 'timesheet')['user_enabled']) { ?>
					<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',timesheet,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="timesheet"/>Time Sheet</label>
				<?php } ?>
				<?php if(tile_enabled($dbc, 'driving_log')['user_enabled']) { ?>
					<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',driving_log,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="driving_log"/>Mileage</label>
				<?php } ?>
				<?php if(tile_enabled($dbc, 'expense')['user_enabled']) { ?>
					<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',expense,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="expense"/>Expenses</label>
				<?php } ?>
				<label class="form-checkbox"><input name="company_sections[]" <?php if (strpos($company_sections, ',other,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="other"/>Other</label>
				<h4>The following fields will be shown or hidden within the Company rate cards:</h4>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',category,') !== FALSE ? "checked" : "" ?> type="checkbox" value="category"/>Rate Card Category</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',ref_card,') !== FALSE ? "checked" : "" ?> type="checkbox" value="ref_card"/>Primary Rate Card</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',start_end_dates,') !== FALSE ? " checked" : "" ?> type="checkbox" value="start_end_dates"/>Start &amp; End Dates</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',reminder_alerts,') !== FALSE ? " checked" : "" ?> type="checkbox" value="reminder_alerts"/>Reminder Alerts (Date and Staff)</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',email_alerts,') !== FALSE ? " checked" : "" ?> type="checkbox" value="email_alerts"/>Email Reminder Alerts</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',tile,') !== FALSE ? "checked" : "" ?> type="checkbox" value="tile"/>Tile Name</label>
				<div class="sortable block-group">
					<script>
					$(document).ready(function() {
						$('.sortable').sortable({
						  connectWith: '.sortable',
						  items: 'label'
						});
					});
					</script>
					<h4>Sortable Fields</h4>
					<em><p>These fields can be dragged to change the order in which they will display</p></em>
					<?php foreach(array_unique(explode('#*#',get_config($dbc,'estimate_field_order').'#*#Type#*#Heading#*#Description#*#Item Type#*#Daily#*#Hourly#*#Customer Price#*#Dollarsaving#*#Percentsaving#*#UOM#*#Quantity#*#Cost#*#Margin#*#Profit#*#Estimate Price#*#Total')) as $field_row) {
						switch(explode('***',$field_row)[0]) {
							case 'Type': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',type,') !== FALSE ? "checked" : "" ?> type="checkbox" value="type"/><input type="hidden" name="field_sort[]" value="Type">Type</label>
								<?php break;
							case 'Heading': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',heading,') !== FALSE ? "checked" : "" ?> type="checkbox" value="heading"/><input type="hidden" name="field_sort[]" value="Heading">Heading</label>
								<?php break;
							case 'Description': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',description,') !== FALSE ? "checked" : "" ?> type="checkbox" value="description"/><input type="hidden" name="field_sort[]" value="Description">Description</label>
								<?php break;
							case 'Item Type': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',itemtype,') !== FALSE ? "checked" : "" ?> type="checkbox" value="itemtype"/><input type="hidden" name="field_sort[]" value="Item Type">Item Type</label>
								<?php break;
							case 'Daily': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',daily,') !== FALSE ? "checked" : "" ?> type="checkbox" value="daily"/><input type="hidden" name="field_sort[]" value="Daily">Daily Rate</label>
								<?php break;
							case 'Hourly': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',hourly,') !== FALSE ? "checked" : "" ?> type="checkbox" value="hourly"/><input type="hidden" name="field_sort[]" value="Hourly">Hourly Rate</label>
								<?php break;
							case 'UOM': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',uom,') !== FALSE ? "checked" : "" ?> type="checkbox" value="uom"/><input type="hidden" name="field_sort[]" value="UOM">UOM</label>
								<?php break;
							case 'Cost': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',cost,') !== FALSE ? "checked" : "" ?> type="checkbox" value="cost"/><input type="hidden" name="field_sort[]" value="Cost">Cost</label>
								<?php break;
							case 'Estimate Price': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',estimate,') !== FALSE ? "checked" : "" ?> type="checkbox" value="estimate"/><input type="hidden" name="field_sort[]" value="Estimate Price">Estimate Price</label>
								<?php break;
							case 'Customer Price': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',customer,') !== FALSE ? "checked" : "" ?> type="checkbox" value="customer"/><input type="hidden" name="field_sort[]" value="Customer Price">Customer Price</label>
								<?php break;
							case 'Quantity': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',quantity,') !== FALSE ? "checked" : "" ?> type="checkbox" value="quantity"/><input type="hidden" name="field_sort[]" value="Quantity">Quantity</label>
								<?php break;
							case 'Total': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',total,') !== FALSE ? "checked" : "" ?> type="checkbox" value="total"/><input type="hidden" name="field_sort[]" value="Total">Total</label>
								<?php break;
							case 'Profit': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',profit,') !== FALSE ? "checked" : "" ?> type="checkbox" value="profit"/><input type="hidden" name="field_sort[]" value="Profit">Profit</label>
								<?php break;
							case 'Margin': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',margin,') !== FALSE ? "checked" : "" ?> type="checkbox" value="margin"/><input type="hidden" name="field_sort[]" value="Margin">Margin</label>
								<?php break;
							case 'Dollarsaving': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',dollarsaving,') !== FALSE ? "checked" : "" ?> type="checkbox" value="dollarsaving"/><input type="hidden" name="field_sort[]" value="Dollarsaving">$ Savings</label>
								<?php break;
							case 'Percentsaving': ?>
								<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',percentsaving,') !== FALSE ? "checked" : "" ?> type="checkbox" value="percentsaving"/><input type="hidden" name="field_sort[]" value="Percentsaving">% Savings</label>
								<?php break;
						}
					} ?>
				</div>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',sort_order,') !== FALSE ? "checked" : "" ?> type="checkbox" value="sort_order"/>Sort Order</label>
				<label class="form-checkbox"><input name="company_fields[]" <?= strpos($field_config, ',breakdown,') !== FALSE ? "checked" : "" ?> type="checkbox" value="breakdown"/>Breakdown</label>
			<?php } else if($tab == 'customer') { ?>
				<?php $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `field_config_ratecard`"));
				$value_config = ','.$get_field_config['config_fields'].',';
				$rcdb_config = ','.$get_field_config['dashboard_fields'].','; ?>
				<div class="block-panels panel-group" id="accordion2">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Customer_Dashboard" >
									Customer Dashboard Fields<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Customer_Dashboard" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($rcdb_config, ','."start_end_dates".',') !== FALSE) { echo " checked"; } ?> value="start_end_dates" style="height: 20px; width: 20px;" name="rcdb_config[]">&nbsp;&nbsp;Start &amp; End Dates&nbsp;&nbsp;
								<input type="checkbox" <?php if (strpos($rcdb_config, ','."alert_date".',') !== FALSE) { echo " checked"; } ?> value="alert_date" style="height: 20px; width: 20px;" name="rcdb_config[]">&nbsp;&nbsp;Alert Date&nbsp;&nbsp;
								<input type="checkbox" <?php if (strpos($rcdb_config, ','."alert_staff".',') !== FALSE) { echo " checked"; } ?> value="alert_staff" style="height: 20px; width: 20px;" name="rcdb_config[]">&nbsp;&nbsp;Alert Staff&nbsp;&nbsp;
								<input type="checkbox" <?php if (strpos($rcdb_config, ','."created_by".',') !== FALSE) { echo " checked"; } ?> value="created_by" style="height: 20px; width: 20px;" name="rcdb_config[]">&nbsp;&nbsp;Created By&nbsp;&nbsp;

							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Customer_Fields" >
									Rate Card Fields<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Customer_Fields" class="panel-collapse collapse">
							<div class="panel-body">

								<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."ref_card".',') !== FALSE) { echo " checked"; } ?> value="ref_card" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Reference Rate Card&nbsp;&nbsp;</label>
								<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."start_end_dates".',') !== FALSE) { echo " checked"; } ?> value="start_end_dates" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Start &amp; End Dates&nbsp;&nbsp;</label>
								<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."reminder_alerts".',') !== FALSE) { echo " checked"; } ?> value="reminder_alerts" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Reminder Alerts (Date and Staff)&nbsp;&nbsp;</label>
								<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."email_alerts".',') !== FALSE) { echo " checked"; } ?> value="email_alerts" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Email Alerts&nbsp;&nbsp;</label>
								<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."savings".',') !== FALSE) { echo " checked"; } ?> value="savings" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Rate Card Savings&nbsp;&nbsp;</label>

							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="Use this setting to add different Contact Categories to the Customer Rate Card."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Customer_Categories" >
									Contact Categories<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Customer_Categories" class="panel-collapse collapse">
							<div class="panel-body">
								<?php
								$cat_config = ','.(get_config($dbc, 'customer_rate_card_contact_categories') ?: 'Business').',';
								$all_cats = array_unique(array_filter(explode(',', get_config($dbc, 'contacts_tabs').','.get_config($dbc, 'contactsrolodex_tabs').','.get_config($dbc, 'contacts3_tabs').','.get_config($dbc, 'clientinfo_tabs').','.get_config($dbc, 'members_tabs').','.get_config($dbc, 'vendors_tabs'))));
								asort($all_cats);
								?>

								<div class="form-group">
									<label class="col-sm-4 control-label">Contact Category:</label>
									<div class="col-sm-8">
										<select name="customer_contact_category[]" multiple class="chosen-select-deselect form-control">
											<?php
											foreach ($all_cats as $contact_cat) {
												echo '<option value="'.$contact_cat.'" '.(strpos($cat_config, ','.$contact_cat.',') !== FALSE ? 'selected' : '').'>'.$contact_cat.'</option>';
											} ?>
										</select>
									</div>
								</div>

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="Ticking this checkbox will add a Frequency Type & Frequency Interval to the Rate Card."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Frequency" >
									Frequency<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Frequency" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Customer Fields Freqeuncy".',') !== FALSE) { echo " checked"; } ?> value="Customer Fields Freqeuncy" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Frequency&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Package" >
									Package<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Package" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Package".',') !== FALSE) { echo " checked"; } ?> value="Package" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Package
								<br><br>

								<input type="checkbox" <?php if (strpos($value_config, ','."Package Service Type".',') !== FALSE) { echo " checked"; } ?> value="Package Service Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
								<input type="checkbox" <?php if (strpos($value_config, ','."Package Category".',') !== FALSE) { echo " checked"; } ?> value="Package Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Package Heading".',') !== FALSE) { echo " checked"; } ?> value="Package Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Promotion" >
									Promotion<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Promotion" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Promotion".',') !== FALSE) { echo " checked"; } ?> value="Promotion" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Promotion
								<br><br>

								<input type="checkbox" <?php if (strpos($value_config, ','."Promotion Service Type".',') !== FALSE) { echo " checked"; } ?> value="Promotion Service Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
								<input type="checkbox" <?php if (strpos($value_config, ','."Promotion Category".',') !== FALSE) { echo " checked"; } ?> value="Promotion Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Promotion Heading".',') !== FALSE) { echo " checked"; } ?> value="Promotion Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Custom" >
									Custom<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Custom" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Custom".',') !== FALSE) { echo " checked"; } ?> value="Custom" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Custom
								<br><br>

								<input type="checkbox" <?php if (strpos($value_config, ','."Custom Service Type".',') !== FALSE) { echo " checked"; } ?> value="Custom Service Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
								<input type="checkbox" <?php if (strpos($value_config, ','."Custom Category".',') !== FALSE) { echo " checked"; } ?> value="Custom Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Custom Heading".',') !== FALSE) { echo " checked"; } ?> value="Custom Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Material" >
									Material<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Material" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Material".',') !== FALSE) { echo " checked"; } ?> value="Material" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Material
								<br><br>

								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Material Code".',') !== FALSE) { echo " checked"; } ?> value="Material Code" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Code&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Services" >
									Services<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Services" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Services".',') !== FALSE) { echo " checked"; } ?> value="Services" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Services
								<br><br>

								<input type="checkbox" <?php if (strpos($value_config, ','."Services Service Type".',') !== FALSE) { echo " checked"; } ?> value="Services Service Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
								<input type="checkbox" <?php if (strpos($value_config, ','."Services Category".',') !== FALSE) { echo " checked"; } ?> value="Services Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Services Heading".',') !== FALSE) { echo " checked"; } ?> value="Services Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;
								<input type="checkbox" <?php if (strpos($value_config, ','."Services Unit of Measurement".',') !== FALSE) { echo " checked"; } ?> value="Services Unit of Measurement" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Unit of Measurement&nbsp;&nbsp;
								<input type="checkbox" <?php if (strpos($value_config, ','."Services Comments".',') !== FALSE) { echo " checked"; } ?> value="Services Comments" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Specific Comments&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Products" >
									Products<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Products" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Products".',') !== FALSE) { echo " checked"; } ?> value="Products" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Products
								<br><br>

								<input type="checkbox" <?php if (strpos($value_config, ','."Products Product Type".',') !== FALSE) { echo " checked"; } ?> value="Products Product Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Product Type&nbsp;&nbsp;
								<input type="checkbox" <?php if (strpos($value_config, ','."Products Category".',') !== FALSE) { echo " checked"; } ?> value="Products Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Products Heading".',') !== FALSE) { echo " checked"; } ?> value="Products Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_sred" >
									SR&ED<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_sred" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."SRED".',') !== FALSE) { echo " checked"; } ?> value="SRED" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;SR&ED
								<br><br>

								<input type="checkbox" <?php if (strpos($value_config, ','."SRED SRED Type".',') !== FALSE) { echo " checked"; } ?> value="SRED SRED Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;SR&ED Type&nbsp;&nbsp;
								<input type="checkbox" <?php if (strpos($value_config, ','."SRED Category".',') !== FALSE) { echo " checked"; } ?> value="SRED Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."SRED Heading".',') !== FALSE) { echo " checked"; } ?> value="SRED Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;
							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Staff" >
									Staff<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Staff" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Staff
								<br><br>

								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Staff Contact Person".',') !== FALSE) { echo " checked"; } ?> value="Staff Contact Person" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Person&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_position" >
									Position<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_position" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Position".',') !== FALSE) { echo " checked"; } ?> value="Position" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Position Rates

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Contractor" >
									Contractor<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Contractor" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Contractor".',') !== FALSE) { echo " checked"; } ?> value="Contractor" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contractor
								<br><br>

								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Contractor Contact Person".',') !== FALSE) { echo " checked"; } ?> value="Contractor Contact Person" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Person&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Clients" >
									Clients<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Clients" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Clients".',') !== FALSE) { echo " checked"; } ?> value="Clients" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Clients
								<br><br>

								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Clients Client Name".',') !== FALSE) { echo " checked"; } ?> value="Clients Client Name" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Client Name&nbsp;&nbsp;

								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Clients Contact Person".',') !== FALSE) { echo " checked"; } ?> value="Clients Contact Person" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Person&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pl" >
									Vendor Pricelist<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_pl" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Vendor Pricelist".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Vendor Pricelist
								<br><br>

								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Vendor Pricelist Vendor".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist Vendor" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Vendor&nbsp;&nbsp;
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Vendor Pricelist Price List".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist Price List" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Price List&nbsp;&nbsp;
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Vendor Pricelist Category".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Vendor Pricelist Product".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist Product" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Product&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Customer" >
									Customer<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Customer" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Customer".',') !== FALSE) { echo " checked"; } ?> value="Customer" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Customer
								<br><br>

								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Customer Customer Name".',') !== FALSE) { echo " checked"; } ?> value="Customer Customer Name" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Customer Name&nbsp;&nbsp;
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Customer Contact Person".',') !== FALSE) { echo " checked"; } ?> value="Customer Contact Person" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Person&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Inventory" >
									Inventory<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Inventory" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Inventory".',') !== FALSE) { echo " checked"; } ?> value="Inventory" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Inventory
								<br><br>

								<input type="checkbox" <?php if (strpos($value_config, ','."Inventory Category".',') !== FALSE) { echo " checked"; } ?> value="Inventory Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
								<input type="checkbox" <?php if (strpos($value_config, ','."Inventory Part Number".',') !== FALSE) { echo " checked"; } ?> value="Inventory Part Number" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Part Number
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Inventory Product Name".',') !== FALSE) { echo " checked"; } ?> value="Inventory Product Name" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Product Name&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Equipment" >
									Equipment<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Equipment" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Equipment
								<br><br>

								<input type="checkbox" <?php if (strpos($value_config, ','."Equipment Category".',') !== FALSE) { echo " checked"; } ?> value="Equipment Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Equipment Unit/Serial Number".',') !== FALSE) { echo " checked"; } ?> value="Equipment Unit/Serial Number" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Unit/Serial Number&nbsp;&nbsp;
								<input type="checkbox" <?php if (strpos($value_config, ','."Equipment Comp".',') !== FALSE) { echo " checked"; } ?> value="Equipment Comp" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?= TICKET_NOUN ?> Compensation&nbsp;&nbsp;
							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Labour" >
									Labour<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Labour" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Labour".',') !== FALSE) { echo " checked"; } ?> value="Labour" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Labour
								<br><br>

								<input type="checkbox" <?php if (strpos($value_config, ','."Labour Type".',') !== FALSE) { echo " checked"; } ?> value="Labour Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Type&nbsp;&nbsp;
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Labour Heading".',') !== FALSE) { echo " checked"; } ?> value="Labour Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Expenses" >
									Expenses<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Expenses" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Expenses".',') !== FALSE) { echo " checked"; } ?> value="Expenses" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Expenses
								<br><br>

								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Expenses Type".',') !== FALSE) { echo " checked"; } ?> value="Expenses Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Type&nbsp;&nbsp;
								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Expenses Category".',') !== FALSE) { echo " checked"; } ?> value="Expenses Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Mileage" >
									Mileage<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Mileage" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Mileage".',') !== FALSE) { echo " checked"; } ?> value="Mileage" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Mileage Rate
								<br><br>

							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Other" >
									Other<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_Other" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Other".',') !== FALSE) { echo " checked"; } ?> value="Other" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Other
								<br><br>

								<input disabled type="checkbox" <?php if (strpos($value_config, ','."Other Detail".',') !== FALSE) { echo " checked"; } ?> value="Other Detail" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Detail&nbsp;&nbsp;
							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_comp" >
									Compensation<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_comp" class="panel-collapse collapse">
							<div class="panel-body">

								<input type="checkbox" <?php if (strpos($value_config, ','."Compensation".',') !== FALSE) { echo " checked"; } ?> value="Compensation" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Individual Compensation
								<input type="checkbox" <?php if (strpos($value_config, ','."Equip Compensation".',') !== FALSE) { echo " checked"; } ?> value="Equip Compensation" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Equipment Compensation
							</div>
						</div>
					</div>
				</div>
			<?php } elseif($tab == 'labour') { ?>
				<h4 class="pad-5"><?php echo $title; ?> Dashboard Fields</h4>
				<label class="form-checkbox"><input type="checkbox" disabled checked value="labour_type" name="db_config[]">Labour Type</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',category,') !== false ? "checked" : ""; ?> value="category" name="db_config[]">Category</label>
				<label class="form-checkbox"><input type="checkbox" disabled checked value="heading" name="db_config[]">Heading</label>
				<label class="form-checkbox"><input type="checkbox" disabled checked value="start_date" name="field_config[]">Start Date</label>
				<label class="form-checkbox"><input type="checkbox" disabled checked value="end_date" name="field_config[]">End Date</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',alert_date,') !== false ? "checked" : ""; ?> value="alert_date" name="db_config[]">Alert Date</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',alert_staff,') !== false ? "checked" : ""; ?> value="alert_staff" name="db_config[]">Alert Staff</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',created_by,') !== false ? "checked" : ""; ?> value="created_by" name="db_config[]">Created By</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',uom,') !== false ? "checked" : ""; ?> value="uom" name="db_config[]">UOM</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',cost,') !== false ? "checked" : ""; ?> value="cost" name="db_config[]">Cost</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',profit_percent,') !== false ? "checked" : ""; ?> value="profit_percent" name="db_config[]">Profit %</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',profit_dollar,') !== false ? "checked" : ""; ?> value="profit_dollar" name="db_config[]">Profit $</label>
				<label class="form-checkbox"><input type="checkbox" disabled checked value="price" name="db_config[]">Price</label>

				<h4 class="pad-5"><?php echo $title; ?> Fields</h4>
				<label class="form-checkbox"><input type="checkbox" disabled checked value="labour_type">Labour Type</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',category,') !== false ? "checked" : ""; ?> value="category" name="field_config[]">Category</label>
				<label class="form-checkbox"><input type="checkbox" disabled checked value="heading">Heading</label>
				<label class="form-checkbox"><input type="checkbox" disabled checked value="start_date">Start Date</label>
				<label class="form-checkbox"><input type="checkbox" disabled checked value="end_date">End Date</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',reminder_alerts,') !== false ? "checked" : ""; ?> value="reminder_alerts" name="field_config[]">Reminder Alerts (Date and Staff)</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',email_alerts,') !== false ? "checked" : ""; ?> value="email_alerts" name="field_config[]">Email Reminder Alerts</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',uom,') !== false ? "checked" : ""; ?> value="uom" name="field_config[]">UOM</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',cost,') !== false ? "checked" : ""; ?> value="cost" name="field_config[]">Cost</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',profit_percent,') !== false ? "checked" : ""; ?> value="profit_percent" name="field_config[]">Profit %</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',profit_dollar,') !== false ? "checked" : ""; ?> value="profit_dollar" name="field_config[]">Profit $</label>
				<label class="form-checkbox"><input type="checkbox" disabled checked value="price">Price</label>
			<?php } elseif($tab == 'services') { ?>
				<h4 class="pad-5"><?php echo $title; ?> Dashboard Fields</h4>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',alert_date,') !== false ? "checked" : ""; ?> value="alert_date" name="db_config[]">Alert Date</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',alert_staff,') !== false ? "checked" : ""; ?> value="alert_staff" name="db_config[]">Alert Staff</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',created_by,') !== false ? "checked" : ""; ?> value="created_by" name="db_config[]">Created By</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',cost,') !== false ? "checked" : ""; ?> value="cost" name="db_config[]">Cost</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',margin,') !== false ? "checked" : ""; ?> value="margin" name="db_config[]">Margin</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',profit,') !== false ? "checked" : ""; ?> value="profit" name="db_config[]">Profit</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',uom,') !== false ? "checked" : ""; ?> value="uom" name="db_config[]">UoM</label>

				<h4 class="pad-5"><?php echo $title; ?> Fields</h4>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',cost,') !== false ? "checked" : ""; ?> value="cost" name="field_config[]">Cost</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',margin,') !== false ? "checked" : ""; ?> value="margin" name="field_config[]">Margin</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',profit,') !== false ? "checked" : ""; ?> value="profit" name="field_config[]">Profit</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',uom,') !== false ? "checked" : ""; ?> value="uom" name="field_config[]">UoM</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',reminder_alerts,') !== false ? "checked" : ""; ?> value="reminder_alerts" name="field_config[]">Reminder Alerts (Date and Staff)</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',email_alerts,') !== false ? "checked" : ""; ?> value="email_alerts" name="field_config[]">Email Reminder Alerts</label>
			<?php }  elseif($tab == 'holiday') { ?>
				<h4 class="pad-5"><?php echo $title; ?> Dashboard Fields</h4>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',holiday_rate_type,') !== false ? "checked" : ""; ?> value="holiday_rate_type" name="db_config[]">Holiday Rate Type</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',holiday_rate_position,') !== false ? "checked" : ""; ?> value="holiday_rate_position" name="db_config[]">Position</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',holiday_rate_staff,') !== false ? "checked" : ""; ?> value="holiday_rate_staff" name="db_config[]">Staff</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',hoilday_rate_hours,') !== false ? "checked" : ""; ?> value="hoilday_rate_hours" name="db_config[]">Number of Hours paid</label>

				<h4 class="pad-5"><?php echo $title; ?> Fields</h4>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',holiday_rate_type,') !== false ? "checked" : ""; ?> value="holiday_rate_type" name="field_config[]">Holiday Rate Type</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',holiday_rate_position,') !== false ? "checked" : ""; ?> value="holiday_rate_position" name="field_config[]">Position</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',holiday_rate_staff,') !== false ? "checked" : ""; ?> value="holiday_rate_staff" name="field_config[]">Staff</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',hoilday_rate_hours,') !== false ? "checked" : ""; ?> value="hoilday_rate_hours" name="field_config[]">Number of Hours paid</label>
			<?php }  elseif($tab == 'expense') { ?>
				<h4 class="pad-5"><?php echo $title; ?> Dashboard Fields</h4>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',expense_rate_type,') !== false ? "checked" : ""; ?> value="expense_rate_type" name="db_config[]">Expense Type</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',expense_rate_position,') !== false ? "checked" : ""; ?> value="expense_rate_position" name="db_config[]">Position</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',expense_rate,') !== false ? "checked" : ""; ?> value="expense_rate" name="db_config[]">Rate paid</label>

				<h4 class="pad-5"><?php echo $title; ?> Fields</h4>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',expense_rate_type,') !== false ? "checked" : ""; ?> value="expense_rate_type" name="field_config[]">Expense Type</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',expense_rate_position,') !== false ? "checked" : ""; ?> value="expense_rate_position" name="field_config[]">Position</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',expense_rate,') !== false ? "checked" : ""; ?> value="expense_rate" name="field_config[]">Rate paid</label>
			<?php } else { ?>
				<h4 class="pad-5"><?php echo $title; ?> Dashboard Fields</h4>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',card,') !== false ? "checked" : ""; ?> value="card" name="db_config[]"><?php echo $title; ?></label>
				<?php if($title == 'Staff'): ?>
					<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',category,') !== false ? "checked" : ""; ?> value="category" name="db_config[]">Category</label>
				<?php endif; ?>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',start_end_dates,') !== false ? "checked" : ""; ?> value="start_end_dates" name="db_config[]">Start &amp; End Dates</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',alert_date,') !== false ? "checked" : ""; ?> value="alert_date" name="db_config[]">Alert Date</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',alert_staff,') !== false ? "checked" : ""; ?> value="alert_staff" name="db_config[]">Alert Staff</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',created_by,') !== false ? "checked" : ""; ?> value="created_by" name="db_config[]">Created By</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',daily,') !== false ? "checked" : ""; ?> value="daily" name="db_config[]">Daily</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',hourly,') !== false ? "checked" : ""; ?> value="hourly" name="db_config[]">Hourly</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',unit_price,') !== false ? "checked" : ""; ?> value="unit_price" name="db_config[]">Unit Price</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',cost,') !== false ? "checked" : ""; ?> value="cost" name="db_config[]">Cost</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',uom,') !== false ? "checked" : ""; ?> value="uom" name="db_config[]">UoM</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',history,') !== false ? "checked" : ""; ?> value="history" name="db_config[]">History</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($db_config,',function,') !== false ? "checked" : ""; ?> value="function" name="db_config[]">Functions</label>

				<h4 class="pad-5"><?php echo $title; ?> Fields</h4>
				<?php if($title == 'Staff'): ?>
					<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',category,') !== false ? "checked" : ""; ?> value="category" name="field_config[]">Category</label>
				<?php endif; ?>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',start_end_dates,') !== false ? "checked" : ""; ?> value="start_end_dates" name="field_config[]">Start &amp; End Dates</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',reminder_alerts,') !== false ? "checked" : ""; ?> value="reminder_alerts" name="field_config[]">Reminder Alerts (Date and Staff)</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',email_alerts,') !== false ? "checked" : ""; ?> value="email_alerts" name="field_config[]">Email Reminder Alerts</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',daily,') !== false ? "checked" : ""; ?> value="daily" name="field_config[]">Daily Rate</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',hourly,') !== false ? "checked" : ""; ?> value="hourly" name="field_config[]">Hourly Rate</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',unit_price,') !== false ? "checked" : ""; ?> value="unit_price" name="field_config[]">Unit Price</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',cost,') !== false ? "checked" : ""; ?> value="cost" name="field_config[]">Cost</label>
				<label class="form-checkbox"><input type="checkbox" <?php echo strpos($field_config,',uom,') !== false ? "checked" : ""; ?> value="uom" name="field_config[]">UoM</label>
			<?php } ?>

			<div class="form-group">
				<div class="col-sm-12">
					<button	type="submit" name="submit"	value="<?php echo $tab; ?>" class="btn brand-btn pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>
</form>