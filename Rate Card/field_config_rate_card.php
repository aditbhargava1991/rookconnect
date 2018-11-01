<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('rate_card');
error_reporting(0);

if (isset($_POST['submit'])) {
	$tab_config = ','.implode(',',$_POST['tab_list']).',';
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) tab_config FROM `general_configuration` WHERE `name`='rate_card_tabs'"));
	if($get_config['tab_config'] > 0) {
		$tab_sql = "UPDATE `general_configuration` SET `value`='$tab_config' WHERE `name`='rate_card_tabs'";
	} else {
		$tab_sql = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('rate_card_tabs','$tab_config')";
	}
	$result_tabs = mysqli_query($dbc, $tab_sql);

	if($_POST['submit'] == 'company' || $_POST['submit'] == 'universal') {
		$rate_card_types = filter_var($_POST['rate_card_types'],FILTER_SANITIZE_STRING);
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(`configid`) AS configid FROM `general_configuration` WHERE `name`='rate_card_types'"));
		if($get_config['configid'] > 0) {
			$query_update_employee = "UPDATE `general_configuration` SET `value` = '$rate_card_types' WHERE `name`='rate_card_types'";
			$result_update_employee = mysqli_query($dbc, $query_update_employee);
		} else {
			$query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('rate_card_types', '$rate_card_types')";
			$result_insert_config = mysqli_query($dbc, $query_insert_config);
		}

		//Field Labels and Order
		$labels = [];
		foreach($_POST['estimate_field_name'] as $key => $name) {
			$labels[] = $name.'***'.$_POST['estimate_field_label'][$key];
		}
		$label_config = implode('#*#',$labels);
		$result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimate_field_order' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimate_field_order') num WHERE num.rows=0");
		$result = mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$label_config' WHERE `name`='estimate_field_order'");
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

	echo '<script type="text/javascript"></script>';
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : '';
switch($tab) {
	case 'universal': $tab = 'universal'; break;
	case 'position': $tab = 'position'; break;
	case 'staff': $tab = 'staff'; break;
	case 'category': $tab = 'category'; break;
	case 'equipment': $tab = 'equipment'; break;
	case 'customer': $tab = 'customer'; break;
	case 'labour': $tab = 'labour'; break;
	case 'services': $tab = 'services'; break;
	default: $tab = 'company'; break;
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
	default:
		$title = "Company";
		$db_config = get_config($dbc, 'company_db_rate_fields');
		$field_config = get_config($dbc, 'company_rate_fields');
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
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1>Rate Cards</h1>
<div class="pad-left gap-top double-gap-bottom"><a href="rate_card.php?card=<?php echo $tab; ?>" class="btn config-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->

<div class="gap-left tab-container">
	<a href="field_config_rate_card.php?tab=universal"><button class="btn brand-btn<?php echo ($tab == 'universal' ? ' active_tab' : ''); ?>">Universal Rates</button></a>
	<a href="field_config_rate_card.php?tab=company"><button class="btn brand-btn<?php echo ($tab == 'company' ? ' active_tab' : ''); ?>">Company</button></a>
	<a href="field_config_rate_card.php?tab=customer"><button class="btn brand-btn<?php echo ($tab == 'customer' ? ' active_tab' : ''); ?>">Customer</button></a>
	<a href="field_config_rate_card.php?tab=position"><button class="btn brand-btn<?php echo ($tab == 'position' ? ' active_tab' : ''); ?>">Position</button></a>
	<a href="field_config_rate_card.php?tab=staff"><button class="btn brand-btn<?php echo ($tab == 'staff' ? ' active_tab' : ''); ?>">Staff</button></a>
	<a href="field_config_rate_card.php?tab=equipment"><button class="btn brand-btn<?php echo ($tab == 'equipment' ? ' active_tab' : ''); ?>">Equipment</button></a>
	<a href="field_config_rate_card.php?tab=category"><button class="btn brand-btn<?php echo ($tab == 'category' ? ' active_tab' : ''); ?>">Equipment Category</button></a>
	<a href="field_config_rate_card.php?tab=labour"><button class="btn brand-btn<?php echo ($tab == 'labour' ? ' active_tab' : ''); ?>">Labour</button></a>
</div>

<form id="form1" name="form1" method="post"	action="field_config_rate_card.php?tab=<?php echo $tab; ?>" enctype="multipart/form-data" class="form-horizontal" role="form">
<?php
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `field_config_ratecard`"));
$value_config = ','.$get_field_config['config_fields'].',';
$rcdb_config = ','.$get_field_config['dashboard_fields'].',';
?>
<div class="panel-group" id="accordion2">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tabs" >
					Available Tabs<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_tabs" class="panel-collapse collapse">
			<div class="panel-body">
				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top;">
				<input type="checkbox" <?php echo strpos($tab_config,',universal,') !== false ? "checked" : ""; ?> value="universal" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="tab_list[]">
				Universal Rates</label><br />
				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top;">
				<input type="checkbox" <?php echo strpos($tab_config,',company,') !== false ? "checked" : ""; ?> value="company" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="tab_list[]">
				Company</label><br />
				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top;">
				<input type="checkbox" <?php echo strpos($tab_config,',customer,') !== false ? "checked" : ""; ?> value="customer" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="tab_list[]">
				Customer</label><br />
				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top;">
				<input type="checkbox" <?php echo strpos($tab_config,',estimate,') !== false ? "checked" : ""; ?> value="estimate" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="tab_list[]">
				Estimate Scope Template</label><br />
				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top;">
				<input type="checkbox" <?php echo strpos($tab_config,',position,') !== false ? "checked" : ""; ?> value="position" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="tab_list[]">
				Position</label><br />
				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top;">
				<input type="checkbox" <?php echo strpos($tab_config,',staff,') !== false ? "checked" : ""; ?> value="staff" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="tab_list[]">
				Staff</label><br />
				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top;">
				<input type="checkbox" <?php echo strpos($tab_config,',equipment,') !== false ? "checked" : ""; ?> value="equipment" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="tab_list[]">
				Equipment</label><br />
				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top;">
				<input type="checkbox" <?php echo strpos($tab_config,',category,') !== false ? "checked" : ""; ?> value="category" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="tab_list[]">
				Equipment Category</label><br />

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top;">
				<input type="checkbox" <?php echo strpos($tab_config,',services,') !== false ? "checked" : ""; ?> value="services" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="tab_list[]">
				Services</label><br />

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top;">
				<input type="checkbox" <?php echo strpos($tab_config,',labour,') !== false ? "checked" : ""; ?> value="labour" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="tab_list[]">
				Labour</label><br />
			</div>
		</div>
	</div>

	<?php if($tab == 'company' || $tab == 'universal'): ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_rc" >
					Rate Card Types<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_rc" class="panel-collapse collapse">
			<div class="panel-body">

				<div class="form-group">
					<label for="fax_number"	class="col-sm-4	control-label">Add Types Separated By a Comma:<br /><small><em>These are the same for Universal and Company Rate Cards.</em></small></label>
					<div class="col-sm-8">
					  <input name="rate_card_types" type="text" value="<?php echo get_config($dbc, 'rate_card_types'); ?>" class="form-control"/>
					</div>
				</div>

			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if($tab == 'company'): ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_db" >
					Company Dashboard<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_db" class="panel-collapse collapse">
			<div class="panel-body">

				<div class="form-group">
					<label class="control-label"><input name="company_db[]" <?php if (strpos($db_config, ',card,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="card" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
					Rate Card Name</label>
				</div>
				<div class="form-group">
					<label class="control-label"><input name="company_db[]" <?php if (strpos($db_config, ',category,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="category" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
					Rate Card Category</label>
				</div>
				<div class="form-group">
					<label class="control-label"><input name="company_db[]" <?php if (strpos($db_config, ',start_end_dates,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="start_end_dates" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
					Start &amp; End Dates</label>
				</div>
				<div class="form-group">
					<label class="control-label"><input name="company_db[]" <?php if (strpos($db_config, ',alert_date,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="alert_date" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
					Alert Date</label>
				</div>
				<div class="form-group">
					<label class="control-label"><input name="company_db[]" <?php if (strpos($db_config, ',alert_staff,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="alert_staff" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
					Alert Staff</label>
				</div>
				<div class="form-group">
					<label class="control-label"><input name="company_db[]" <?php if (strpos($db_config, ',total_cost,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="total_cost" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
					Total Cost</label>
				</div>

			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if($tab == 'company' || $tab == 'universal'): ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_fields" >
					<?php echo ($tab == 'company' ? 'Company' : 'Universal'); ?> Fields<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_fields" class="panel-collapse collapse">
			<div class="panel-body">
				<h4>The following fields will be shown or hidden within the rate cards.</h4>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',category,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="category" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Rate Card Category</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',start_end_dates,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="start_end_dates" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Start &amp; End Dates</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',reminder_alerts,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="reminder_alerts" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Reminder Alerts (Date and Staff)</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',email_alerts,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="email_alerts" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Email Reminder Alerts</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',tile,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="tile" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Tile Name</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',type,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="type" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Type</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',heading,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="heading" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Heading</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',description,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="description" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Description</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',itemtype,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="itemtype" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Item Type</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',daily,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="daily" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Daily Rate</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',hourly,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="hourly" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Hourly Rate</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',uom,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="uom" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				UOM</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',cost,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="cost" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Cost</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',estimate,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="estimate" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Estimate Price</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',customer,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="customer" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Customer Price</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',quantity,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="quantity" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Quantity</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',total,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="total" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Total</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',profit,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="profit" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Profit</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',margin,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="margin" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Margin</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',sort_order,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="sort_order" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Sort Order</label>
				<label class="form-checkbox"><input name="company_fields[]" <?php if (strpos($field_config, ',breakdown,') !== FALSE) { echo " checked"; } ?> type="checkbox" value="breakdown" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;"/>
				Breakdown</label>

			</div>
		</div>
	</div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field_order" >
                    Field Display Order<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_field_order" class="panel-collapse collapse">
            <div class="panel-body">
				<h3>Field Sort Order and Labels</h3>
				These settings will affect the Rate Card, Estimates, and Projects. Move the fields around to change the display order.
				<div class='sortable' style='border:solid 1px black;'>
					<style>
					.sortable label {
						background-color: RGBA(255,255,255,0.2);
						margin: 0.5em;
						min-width: 25em;
						padding: 0.5em;
					}
					.sortable label input[type=checkbox] {
						height: 1.5em;
						margin: 0.25em;
						width: 1.5em;
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
					$defaults = 'Type,Heading,Category***Category (If Applicable),Description,Detail,Billing Frequency,Item Type,Daily,Hourly,Customer Price,UOM,Quantity,Cost,Margin,Profit,Estimate Price,Total,';
					foreach($estimate_field_order as $value) {
						$defaults = trim(str_replace(','.explode('***',$value)[0].',',',',','.$defaults.','),',');
					}
					$estimate_field_arr = array_filter(array_unique(array_merge($estimate_field_order,explode(',',$defaults))));
					foreach($estimate_field_arr as $field_order) {
						$data = explode('***', $field_order);
						$field = $data[0];
						$label = $data[1];
						echo '<label><input type="checkbox" '.(in_array($field_order,$estimate_field_order) ? 'checked' : '').' value="'.$field.'" name="estimate_field_name[]">';
						echo $field.': <input type="text" '.(in_array($field_order,$estimate_field_order) ? '' : 'disabled').' class="form-control" name="estimate_field_label[]" value="'.$label.'"></label>';
					} ?>
				</div>
            </div>
        </div>
    </div>
	<?php elseif($tab == 'customer'): ?>
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
					Customer Fields<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_Customer_Fields" class="panel-collapse collapse">
			<div class="panel-body">

				<input type="checkbox" <?php if (strpos($value_config, ','."start_end_dates".',') !== FALSE) { echo " checked"; } ?> value="start_end_dates" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Start &amp; End Dates&nbsp;&nbsp;
				<input type="checkbox" <?php if (strpos($value_config, ','."reminder_alerts".',') !== FALSE) { echo " checked"; } ?> value="reminder_alerts" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Reminder Alerts (Date and Staff)&nbsp;&nbsp;
				<input type="checkbox" <?php if (strpos($value_config, ','."email_alerts".',') !== FALSE) { echo " checked"; } ?> value="email_alerts" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Email Alerts&nbsp;&nbsp;

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
	            $cat_config = ','.get_config($dbc, 'customer_rate_card_contact_categories').',';
	            if($cat_config == ',,') {
	            	$cat_config = ',Business,';
	            }
	            $all_cats = get_config($dbc, 'contacts_tabs').','.get_config($dbc, 'contactsrolodex_tabs').','.get_config($dbc, 'contacts3_tabs').','.get_config($dbc, 'clientinfo_tabs').','.get_config($dbc, 'members_tabs').','.get_config($dbc, 'vendors_tabs');
                $all_cats = array_unique(array_filter(explode(',', $all_cats)));
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
	<?php elseif($tab == 'labour'): ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_db" >
					<?= $title ?> Dashboard<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_db" class="panel-collapse collapse">
			<div class="panel-body">

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" disabled checked value="labour_type" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
				Labour Type</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($db_config,',category,') !== false ? "checked" : ""; ?> value="category" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
				Category</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" disabled checked value="heading" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
				Heading</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" disabled checked value="start_date" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				Start Date</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" disabled checked value="end_date" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				End Date</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($db_config,',alert_date,') !== false ? "checked" : ""; ?> value="alert_date" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
				Alert Date</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($db_config,',alert_staff,') !== false ? "checked" : ""; ?> value="alert_staff" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
				Alert Staff</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($db_config,',created_by,') !== false ? "checked" : ""; ?> value="created_by" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
				Created By</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($db_config,',uom,') !== false ? "checked" : ""; ?> value="uom" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
				UOM</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($db_config,',cost,') !== false ? "checked" : ""; ?> value="cost" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
				Cost</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($db_config,',profit_percent,') !== false ? "checked" : ""; ?> value="profit_percent" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
				Profit %</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($db_config,',profit_dollar,') !== false ? "checked" : ""; ?> value="profit_dollar" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
				Profit $</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" disabled checked value="price" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
				Price</label>

			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_fields" >
					<?= $title ?> Fields<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_fields" class="panel-collapse collapse">
			<div class="panel-body">

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" disabled checked value="labour_type" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				Labour Type</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($field_config,',category,') !== false ? "checked" : ""; ?> value="category" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				Category</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" disabled checked value="heading" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				Heading</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" disabled checked value="start_date" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				Start Date</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" disabled checked value="end_date" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				End Date</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($field_config,',reminder_alerts,') !== false ? "checked" : ""; ?> value="reminder_alerts" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				Reminder Alerts (Date and Staff)</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($field_config,',email_alerts,') !== false ? "checked" : ""; ?> value="email_alerts" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				Email Reminder Alerts</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($field_config,',uom,') !== false ? "checked" : ""; ?> value="uom" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				UOM</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($field_config,',cost,') !== false ? "checked" : ""; ?> value="cost" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				Cost</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($field_config,',profit_percent,') !== false ? "checked" : ""; ?> value="profit_percent" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				Profit %</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" <?php echo strpos($field_config,',profit_dollar,') !== false ? "checked" : ""; ?> value="profit_dollar" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				Profit $</label>

				<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
				<input type="checkbox" disabled checked value="price" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
				Price</label>

			</div>
		</div>
	</div>
	<?php elseif($tab == 'services'): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_db" >
						<?php echo $title; ?> Dashboard<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_db" class="panel-collapse collapse">
				<div class="panel-body">
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',alert_date,') !== false ? "checked" : ""; ?> value="alert_date" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Alert Date</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',alert_staff,') !== false ? "checked" : ""; ?> value="alert_staff" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Alert Staff</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',created_by,') !== false ? "checked" : ""; ?> value="created_by" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Created By</label>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_fields" >
						<?php echo $title; ?> Fields<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_fields" class="panel-collapse collapse">
				<div class="panel-body">
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',reminder_alerts,') !== false ? "checked" : ""; ?> value="reminder_alerts" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Reminder Alerts (Date and Staff)</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',email_alerts,') !== false ? "checked" : ""; ?> value="email_alerts" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Email Reminder Alerts</label>
				</div>
			</div>
		</div>
	<?php else: ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_db" >
						<?php echo $title; ?> Dashboard<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_db" class="panel-collapse collapse">
				<div class="panel-body">
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',card,') !== false ? "checked" : ""; ?> value="card" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					<?php echo $title; ?></label>
					<?php if($title == 'Staff'): ?>
						<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
						<input type="checkbox" <?php echo strpos($db_config,',category,') !== false ? "checked" : ""; ?> value="category" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
						Category</label>
					<?php endif; ?>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',start_end_dates,') !== false ? "checked" : ""; ?> value="start_end_dates" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Start &amp; End Dates</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',alert_date,') !== false ? "checked" : ""; ?> value="alert_date" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Alert Date</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',alert_staff,') !== false ? "checked" : ""; ?> value="alert_staff" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Alert Staff</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',created_by,') !== false ? "checked" : ""; ?> value="created_by" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Created By</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',annual,') !== false ? "checked" : ""; ?> value="annual" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Annual</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',monthly,') !== false ? "checked" : ""; ?> value="monthly" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Monthly</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',semi_month,') !== false ? "checked" : ""; ?> value="semi_month" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Semi-Monthly</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',weekly,') !== false ? "checked" : ""; ?> value="weekly" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Weekly</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',daily,') !== false ? "checked" : ""; ?> value="daily" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Daily</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',hourly,') !== false ? "checked" : ""; ?> value="hourly" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Hourly</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',hourly_work,') !== false ? "checked" : ""; ?> value="hourly_work" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Hourly (Work)</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',hourly_travel,') !== false ? "checked" : ""; ?> value="hourly_travel" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Hourly (Travel)</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',field_day_actual,') !== false ? "checked" : ""; ?> value="field_day_actual" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Field Day (Cost)</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',field_day_bill,') !== false ? "checked" : ""; ?> value="field_day_bill" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Field Day (Billable)</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',cost,') !== false ? "checked" : ""; ?> value="cost" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Cost</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',price_admin,') !== false ? "checked" : ""; ?> value="price_admin" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Admin Price</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',price_wholesale,') !== false ? "checked" : ""; ?> value="price_wholesale" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Wholesale Price</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',price_commercial,') !== false ? "checked" : ""; ?> value="price_commercial" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Commercial Price</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',price_client,') !== false ? "checked" : ""; ?> value="price_client" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Client Price</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',minimum,') !== false ? "checked" : ""; ?> value="minimum" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Minimum</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',unit_price,') !== false ? "checked" : ""; ?> value="unit_price" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Unit Price</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',unit_cost,') !== false ? "checked" : ""; ?> value="unit_cost" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Unit Cost</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',rent_price,') !== false ? "checked" : ""; ?> value="rent_price" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Rent Price</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',rent_days,') !== false ? "checked" : ""; ?> value="rent_days" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Rental Days</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',rent_weeks,') !== false ? "checked" : ""; ?> value="rent_weeks" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Rental Weeks</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',rent_months,') !== false ? "checked" : ""; ?> value="rent_months" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Rental Months</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',rent_years,') !== false ? "checked" : ""; ?> value="rent_years" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Rental Years</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',num_days,') !== false ? "checked" : ""; ?> value="num_days" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Number of Days</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',num_hours,') !== false ? "checked" : ""; ?> value="num_hours" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Number of Hours</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',num_kms,') !== false ? "checked" : ""; ?> value="num_kms" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Number of KMs</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',num_miles,') !== false ? "checked" : ""; ?> value="num_miles" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Number of Miles</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',fee,') !== false ? "checked" : ""; ?> value="fee" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Fee</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',hours_estimated,') !== false ? "checked" : ""; ?> value="hours_estimated" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Estimated Hours</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',hours_actual,') !== false ? "checked" : ""; ?> value="hours_actual" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Actual Hours</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',service_code,') !== false ? "checked" : ""; ?> value="service_code" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Service Code</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',description,') !== false ? "checked" : ""; ?> value="description" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Description</label>
					<?php if($title == 'Staff'): ?>
						<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
						<input type="checkbox" <?php echo strpos($db_config,',work_desc,') !== false ? "checked" : ""; ?> value="work_desc" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
						Description of Work</label>
					<?php endif; ?>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',history,') !== false ? "checked" : ""; ?> value="history" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					History</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($db_config,',function,') !== false ? "checked" : ""; ?> value="function" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="db_config[]">
					Functions</label>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_fields" >
						<?php echo $title; ?> Fields<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_fields" class="panel-collapse collapse">
				<div class="panel-body">
					<?php if($title == 'Staff'): ?>
						<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
						<input type="checkbox" <?php echo strpos($field_config,',category,') !== false ? "checked" : ""; ?> value="category" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
						Category</label>
					<?php endif; ?>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',start_end_dates,') !== false ? "checked" : ""; ?> value="start_end_dates" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Start &amp; End Dates</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',reminder_alerts,') !== false ? "checked" : ""; ?> value="reminder_alerts" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Reminder Alerts (Date and Staff)</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',email_alerts,') !== false ? "checked" : ""; ?> value="email_alerts" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Email Reminder Alerts</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',annual,') !== false ? "checked" : ""; ?> value="annual" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Annual Rate</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',monthly,') !== false ? "checked" : ""; ?> value="monthly" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Monthly Rate</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',semi_month,') !== false ? "checked" : ""; ?> value="semi_month" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Semi-Monthly Rate</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',weekly,') !== false ? "checked" : ""; ?> value="weekly" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Weekly Rate</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',daily,') !== false ? "checked" : ""; ?> value="daily" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Daily Rate</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',hourly,') !== false ? "checked" : ""; ?> value="hourly" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Hourly Rate</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',hourly_work,') !== false ? "checked" : ""; ?> value="hourly_work" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Hourly Rate (Work)</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',hourly_travel,') !== false ? "checked" : ""; ?> value="hourly_travel" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Hourly Rate (Travel)</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',field_day_actual,') !== false ? "checked" : ""; ?> value="field_day_actual" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Field Day Rate (Cost)</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',field_day_bill,') !== false ? "checked" : ""; ?> value="field_day_bill" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Field Day Rate (Billable)</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',cost,') !== false ? "checked" : ""; ?> value="cost" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Cost</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',price_admin,') !== false ? "checked" : ""; ?> value="price_admin" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Admin Price</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',price_wholesale,') !== false ? "checked" : ""; ?> value="price_wholesale" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Wholesale Price</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',price_commercial,') !== false ? "checked" : ""; ?> value="price_commercial" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Commercial Price</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',price_client,') !== false ? "checked" : ""; ?> value="price_client" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Client Price</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',minimum,') !== false ? "checked" : ""; ?> value="minimum" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Minimum Billable</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',unit_price,') !== false ? "checked" : ""; ?> value="unit_price" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Unit Price</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',unit_cost,') !== false ? "checked" : ""; ?> value="unit_cost" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Unit Cost</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',rent_price,') !== false ? "checked" : ""; ?> value="rent_price" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Rent Price</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',rent_days,') !== false ? "checked" : ""; ?> value="rent_days" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Rental Days</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',rent_weeks,') !== false ? "checked" : ""; ?> value="rent_weeks" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Rental Weeks</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',rent_months,') !== false ? "checked" : ""; ?> value="rent_months" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Rental Months</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',rent_years,') !== false ? "checked" : ""; ?> value="rent_years" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Rental Years</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',num_days,') !== false ? "checked" : ""; ?> value="num_days" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Number of Days</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',num_hours,') !== false ? "checked" : ""; ?> value="num_hours" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Number of Hours</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',num_kms,') !== false ? "checked" : ""; ?> value="num_kms" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Number of Kilometres</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',num_miles,') !== false ? "checked" : ""; ?> value="num_miles" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Number of Miles</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',fee,') !== false ? "checked" : ""; ?> value="fee" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Fee</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',hours_estimated,') !== false ? "checked" : ""; ?> value="hours_estimated" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Estimated Hours</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',hours_actual,') !== false ? "checked" : ""; ?> value="hours_actual" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Actual Hours</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',service_code,') !== false ? "checked" : ""; ?> value="service_code" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Service Code</label>
					<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
					<input type="checkbox" <?php echo strpos($field_config,',description,') !== false ? "checked" : ""; ?> value="description" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
					Rate Description</label>
					<?php if($title == 'Staff'): ?>
						<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
						<input type="checkbox" <?php echo strpos($field_config,',work_desc,') !== false ? "checked" : ""; ?> value="work_desc" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
						Description of Work</label>
						<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
						<input type="checkbox" <?php echo strpos($field_config,',color_code,') !== false ? "checked" : ""; ?> value="color_code" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
						Color Code</label>
						<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
						<input type="checkbox" <?php echo strpos($field_config,',sort_order,') !== false ? "checked" : ""; ?> value="sort_order" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
						Sort Order</label>
						<label style="margin: 0; padding: 0 1em 1em; text-transform: capitalize; vertical-align: top; width: 15em;">
						<input type="checkbox" <?php echo strpos($field_config,',travel_ranges,') !== false ? "checked" : ""; ?> value="travel_ranges" style="height: 20px; margin: 0 1em; vertical-align: top; width: 20px;" name="field_config[]">
						Hourly Rate Ranges (Travel)</label>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div><br /><div class="clearfix"></div>

<div class="form-group">
	<div class="col-sm-6">
		<a href="rate_card.php?card=<?php echo $tab; ?>" class="btn config-btn btn-lg">Back</a>
		<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
	</div>
	<div class="col-sm-6">
		<button	type="submit" name="submit"	value="<?php echo $tab; ?>" class="btn config-btn btn-lg pull-right">Submit</button>
	</div>
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>