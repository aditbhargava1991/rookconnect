<?php /* Field Configuration for Expenses */
include ('../include.php');
checkAuthorised('expense');
error_reporting(0);

if (isset($_POST['submit'])) {
    $expense_mode = $_POST['expense_mode'];
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'expense_mode' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='expense_mode') num WHERE num.rows = 0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$expense_mode' WHERE `name`='expense_mode'");

    $expense = implode(',',$_POST['expense']);
    $expense_dashboard = implode(',',$_POST['expense_dashboard']);

	$tab = $_GET['tab'];
    $expense_types = filter_var($_POST['expense_types'],FILTER_SANITIZE_STRING);
    $hst_name = filter_var($_POST['hst_name'],FILTER_SANITIZE_STRING);
    $pst_name = filter_var($_POST['pst_name'],FILTER_SANITIZE_STRING);
    $gst_name = filter_var($_POST['gst_name'],FILTER_SANITIZE_STRING);
    $hst_amt = filter_var($_POST['hst_amt'],FILTER_SANITIZE_STRING);
    $pst_amt = filter_var($_POST['pst_amt'],FILTER_SANITIZE_STRING);
    $gst_amt = filter_var($_POST['gst_amt'],FILTER_SANITIZE_STRING);
    $expense_rows = filter_var($_POST['expense_rows'],FILTER_SANITIZE_STRING);
	$exchange_buffer = filter_var($_POST['exchange_buffer'] / 100,FILTER_SANITIZE_STRING);

    $expense_tabs = implode(',',$_POST['expense_tabs']);
	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config_expense WHERE `tab`='$tab'"));
    if($get_field_config['fieldconfigid'] > 0) {
        $query_update_config = "UPDATE `field_config_expense` SET expense = '$expense', expense_dashboard = '$expense_dashboard', `exchange_buffer`='$exchange_buffer', `gst_name`='$gst_name', `pst_name`='$pst_name', `hst_name`='$hst_name', `gst_amt`='$gst_amt', `pst_amt`='$pst_amt', `hst_amt`='$hst_amt', `expense_types`='$expense_types', `expense_rows`='$expense_rows' WHERE `tab`='$tab'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `field_config_expense` (`expense`, `expense_dashboard`, `exchange_buffer`, `gst_name`, `pst_name`, `hst_name`, `gst_amt`, `pst_amt`, `hst_amt`, `expense_types`, `expense_rows`, `tab`)
			VALUES ('$expense', '$expense_dashboard', '$exchange_buffer', '$gst_name', '$pst_name', '$hst_name', '$gst_amt', '$pst_amt', '$hst_amt', '$expense_types', '$expense_rows', '$tab')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_tabs'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$expense_tabs' WHERE name='expense_tabs'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_tabs', '$expense_tabs')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$default_country = filter_var($_POST['default_country'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='default_country'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$default_country' WHERE name='default_country'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('default_country', '$default_country')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$default_province = filter_var($_POST['default_province'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='default_province'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$default_province' WHERE name='default_province'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('default_province', '$default_province')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$expense_reminder_days = filter_var($_POST['expense_reminder_days'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_reminder_days'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$expense_reminder_days' WHERE name='expense_reminder_days'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_reminder_days', '$expense_reminder_days')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$expense_reminder_sender = filter_var($_POST['expense_reminder_sender'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_reminder_sender'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$expense_reminder_sender' WHERE name='expense_reminder_sender'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_reminder_sender', '$expense_reminder_sender')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$expense_reminder_subject = filter_var($_POST['expense_reminder_subject'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_reminder_subject'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$expense_reminder_subject' WHERE name='expense_reminder_subject'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_reminder_subject', '$expense_reminder_subject')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$expense_reminder_body = filter_var(htmlentities($_POST['expense_reminder_body']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_reminder_body'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$expense_reminder_body' WHERE name='expense_reminder_body'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_reminder_body', '$expense_reminder_body')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$expense_provinces = [];
	foreach($_POST['province_code'] as $row => $value) {
		if(trim($value) != '') {
			$expense_provinces[] = filter_var($value,FILTER_SANITIZE_STRING).'*'.
				filter_var($_POST['province_gst'][$row],FILTER_SANITIZE_STRING).'*'.
				filter_var($_POST['province_pst'][$row],FILTER_SANITIZE_STRING).'*'.
				filter_var($_POST['province_hst'][$row],FILTER_SANITIZE_STRING);
		}
	}
	$expense_provinces = implode('#*#',$expense_provinces);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_provinces'"));
    if($get_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET value = '$expense_provinces' WHERE name='expense_provinces'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_provinces', '$expense_provinces')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    //Logo
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    $expense_logo = htmlspecialchars($_FILES["logo"]["name"], ENT_QUOTES);

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_logo'"));
    if($get_config['configid'] > 0) {
		if($expense_logo == '') {
			$logo_update = $_POST['logo_file'];
		} else {
			$logo_update = $expense_logo;
		}
		move_uploaded_file($_FILES["logo"]["tmp_name"],"download/" . $logo_update);
        $query_update_employee = "UPDATE `general_configuration` SET value = '$logo_update' WHERE name='expense_logo'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
		move_uploaded_file($_FILES["logo"]["tmp_name"], "download/" . $_FILES["logo"]["name"]) ;
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_logo', '$expense_logo')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    //Logo

    //Header
    $survey = htmlentities($_POST['expense_header']);
    $expense_header = filter_var($survey,FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_header'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$expense_header' WHERE name='expense_header'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_header', '$expense_header')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Header

    //Footer
    $survey = htmlentities($_POST['expense_footer']);
    $expense_footer = filter_var($survey,FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='expense_footer'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$expense_footer' WHERE name='expense_footer'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('expense_footer', '$expense_footer')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Footer

	// Categories
	$all_ids = implode(',',$_POST['cat_id']);
	$delete_rows = mysqli_query($dbc, "UPDATE `expense_categories` SET `deleted`=0 WHERE `expense_tab`='$tab' AND `categoryid` NOT IN ($all_ids)");
  $before_change = "";
	$history = "Expense entry has been updated. <br />";
	add_update_history($dbc, 'expenses_history', $history, '', $before_change);
	foreach($_POST['cat_id'] as $row => $id) {
		$category = $_POST['category'][$row];
		$heading = $_POST['cat_heading'][$row];
		$amount = $_POST['cat_amount'][$row];
		$monthly = $_POST['cat_monthly'][$row];
		$q1 = $_POST['cat_q1'][$row];
		$q2 = $_POST['cat_q2'][$row];
		$q3 = $_POST['cat_q3'][$row];
		$q4 = $_POST['cat_q4'][$row];
		$annual = $_POST['cat_annual'][$row];

		if($category != '' || $heading != '') {
			$ec = mysqli_fetch_array(mysqli_query($dbc, "SELECT `EC` FROM `expense_categories` WHERE `category`='$category' AND `expense_tab`='$tab' UNION SELECT IFNULL(MAX(`EC`),0) + 1000 FROM `expense_categories` WHERE `expense_tab`='$tab'"))['EC'];
			$gl = mysqli_fetch_array(mysqli_query($dbc, "SELECT `GL` FROM `expense_categories` WHERE `categoryid`='$id' UNION SELECT IFNULL(MAX(`GL`),$ec) + 1 FROM `expense_categories` WHERE `expense_tab`='$tab' AND `category`='$category'"))['GL'];
      $before_change = "";
			if($id == '') {
				$query = "INSERT INTO `expense_categories` (`expense_tab`, `category`, `EC`, `heading`, `GL`, `amount`, `monthly`, `q1`, `q2`, `q3`, `q4`, `annual`)
					VALUES ('$tab', '$category', '$ec', '$heading', '$gl', '$amount', '$monthly', '$q1', '$q2', '$q3', '$q4', '$annual')";
        $history = "Expense catogries entry has been added. <br />";
			} else {
				$query = "UPDATE `expense_categories` SET `expense_tab`='$tab', `category`='$category', `EC`='$ec', `heading`='$heading', `GL`='$gl', `amount`='$amount', `monthly`='$monthly', `q1`='$q1', `q2`='$q2', `q3`='$q3', `q4`='$q4', `annual`='$annual' WHERE `categoryid`='$id'";
        $history = "Expense catogries entry has been updated. <br />";
			}
			mysqli_query($dbc, $query);
      add_update_history($dbc, 'expenses_history', $history, '', $before_change);
		}
	}
	// Categories

    echo '<script type="text/javascript"> window.location.replace("expenses.php?tab='.$tab.'"); </script>';
}

// Variables
$tab_config = ','.get_config($dbc, 'expense_tabs').',';
if(empty($tab_config)) {
	$tab_config = ',budget,current_month,business,customers,clients,staff,sales,manager,payables,report,';
}

$tab = (empty($_GET['tab']) ? explode(',',trim($tab_config))[0] : $_GET['tab']);

$config_sql = "SELECT expense, expense_dashboard, exchange_buffer, gst_name, gst_amt, pst_name, pst_amt, hst_name, hst_amt, expense_types, expense_rows FROM field_config_expense WHERE `tab`='$tab' UNION SELECT
	'Flight,Hotel,Breakfast,Lunch,Dinner,Beverages,Transportation,Entertainment,Gas,Misc',
	'Description,Date,Receipt,Type,Day Expense,Amount,Tax,Total', '0', 'GST', '5', 'PST', '0', 'HST', '0', 'Meals,Tip', 1";
$get_expense_config = mysqli_fetch_assoc(mysqli_query($dbc,$config_sql));
$value_config = ','.$get_expense_config['expense'].',';
$db_config = ','.$get_expense_config['expense_dashboard'].',';
$gst_name = trim($get_expense_config['gst_name'],',');
$pst_name = trim($get_expense_config['pst_name'],',');
$hst_name = trim($get_expense_config['hst_name'],',');
$gst_amt = trim($get_expense_config['gst_amt'],',');
$pst_amt = trim($get_expense_config['pst_amt'],',');
$hst_amt = trim($get_expense_config['hst_amt'],',');
$expense_types = trim(','.$get_expense_config['expense_types'].',',',');
$expense_rows = $get_expense_config['expense_rows'];
$exchange_buffer = $get_expense_config['exchange_buffer'];

?>
</head>
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
	margin: 0.5em;
	min-width: 15em;
	padding: 0.5em;
}
</style>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1>Expense Settings</h1>
<div class="pad-left gap-top double-gap-bottom"><a href="expenses.php?tab=<?php echo $tab; ?>" class="btn brand-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<div class="tab-container mobile-100-container">
	<?php if ( check_subtab_persmission($dbc, 'expense', ROLE, 'budget') === TRUE && strpos($tab_config,',budget,') !== FALSE) { ?>
		<a href="?tab=budget"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($tab == 'budget' ? 'active_tab' : ''); ?>">Budget Expense Tracking</button></a>
	<?php }
	if ( check_subtab_persmission($dbc, 'expense', ROLE, 'current_month') === TRUE && strpos($tab_config,',current_month,') !== FALSE) { ?>
		<a href="?tab=current_month"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($tab == 'current_month' ? 'active_tab' : ''); ?>">Current Month</button></a>
	<?php }
	if ( check_subtab_persmission($dbc, 'expense', ROLE, 'business') === TRUE && strpos($tab_config,',business,') !== FALSE) { ?>
		<a href="?tab=business"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($tab == 'business' ? 'active_tab' : ''); ?>">Business</button></a>
	<?php }
	if ( check_subtab_persmission($dbc, 'expense', ROLE, 'customers') === TRUE && strpos($tab_config,',customers,') !== FALSE) { ?>
		<a href="?tab=customers"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($tab == 'customers' ? 'active_tab' : ''); ?>">Customers</button></a>
	<?php }
	if ( check_subtab_persmission($dbc, 'expense', ROLE, 'clients') === TRUE && strpos($tab_config,',clients,') !== FALSE) { ?>
		<a href="?tab=clients"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($tab == 'clients' ? 'active_tab' : ''); ?>">Clients</button></a>
	<?php }
	if ( check_subtab_persmission($dbc, 'expense', ROLE, 'staff') === TRUE && strpos($tab_config,',staff,') !== FALSE) { ?>
		<a href="?tab=staff"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($tab == 'staff' ? 'active_tab' : ''); ?>">Staff</button></a>
	<?php }
	if ( check_subtab_persmission($dbc, 'expense', ROLE, 'sales') === TRUE && strpos($tab_config,',sales,') !== FALSE) { ?>
		<a href="?tab=sales"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($tab == 'sales' ? 'active_tab' : ''); ?>">Sales</button></a>
	<?php }
	if ( check_subtab_persmission($dbc, 'expense', ROLE, 'manager') === TRUE && strpos($tab_config,',manager,') !== FALSE) { ?>
		<a href="?tab=manager"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($tab == 'manager' ? 'active_tab' : ''); ?>">Manager Approval</button></a>
	<?php }
	if ( check_subtab_persmission($dbc, 'expense', ROLE, 'payables') === TRUE && strpos($tab_config,',payables,') !== FALSE) { ?>
		<a href="?tab=payables"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($tab == 'payables' ? 'active_tab' : ''); ?>">Payables</button></a>
	<?php }
	if ( check_subtab_persmission($dbc, 'expense', ROLE, 'report') === TRUE && strpos($tab_config,',report,') !== FALSE) { ?>
		<a href="?tab=report"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($tab == 'report' ? 'active_tab' : ''); ?>">Reporting</button></a>
	<?php } ?>
</div>

<?php if($tab == 'budget'):
	$budget_link = tile_data($dbc, 'budget'); ?>
	<div class="notice double-gap-bottom popover-examples">
		<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
		<div class="col-sm-11"><span class="notice-name">NOTE:</span>
			The Budget Expense Tracking tab comes from the Budget tile. Budgets, reporting, and settings can be found in that tile,
			and are not available in this tile, which only shows and adds expenses for the existing budgets. To change settings or add a budget,
			<?php echo ($budget_link['link'] !== FALSE ? '<a href="'.WEBSITE_URL.'/'.$budget_link['link'].'">click here</a> to ' : ''); ?>go to the Budget tile.</div>
		<div class="clearfix"></div>
	</div>
<?php endif; ?>

<div class="panel-group" id="accordion2">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_config" >
					Expense Configuration<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_config" class="panel-collapse collapse">
			<div class="panel-body">
				<h3>Expense Tile Mode</h3>
				<label class="form-checkbox"><input type="radio" name="expense_mode" value="tables" checked> Table Mode</label>
				<label class="form-checkbox"><input type="radio" name="expense_mode" value="inbox"> Inbox Mode</label>
				<h3>Enable Tabs</h3>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($tab_config, ','."budget".',') !== FALSE) { echo " checked"; } ?> value="budget" style="height: 20px; width: 20px;" name="expense_tabs[]"> Budget Expense Tracking</label>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($tab_config, ','."current_month".',') !== FALSE) { echo " checked"; } ?> value="current_month" style="height: 20px; width: 20px;" name="expense_tabs[]"> Current Month</label>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($tab_config, ','."business".',') !== FALSE) { echo " checked"; } ?> value="business" style="height: 20px; width: 20px;" name="expense_tabs[]"> Business Expenses</label>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($tab_config, ','."customers".',') !== FALSE) { echo " checked"; } ?> value="customers" style="height: 20px; width: 20px;" name="expense_tabs[]"> Customer Expenses</label>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($tab_config, ','."clients".',') !== FALSE) { echo " checked"; } ?> value="clients" style="height: 20px; width: 20px;" name="expense_tabs[]"> Client Expenses</label>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($tab_config, ','."staff".',') !== FALSE) { echo " checked"; } ?> value="staff" style="height: 20px; width: 20px;" name="expense_tabs[]"> Staff Expenses</label>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($tab_config, ','."sales".',') !== FALSE) { echo " checked"; } ?> value="sales" style="height: 20px; width: 20px;" name="expense_tabs[]"> Sales Expenses</label>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($tab_config, ','."manager".',') !== FALSE) { echo " checked"; } ?> value="manager" style="height: 20px; width: 20px;" name="expense_tabs[]"> Require Manager Approval</label>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($tab_config, ','."payables".',') !== FALSE) { echo " checked"; } ?> value="payables" style="height: 20px; width: 20px;" name="expense_tabs[]"> Payables</label>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($tab_config, ','."report".',') !== FALSE) { echo " checked"; } ?> value="report" style="height: 20px; width: 20px;" name="expense_tabs[]"> Enable Reporting</label>
				<h3>Expense Defaults</h3>
				<label class="col-sm-4 control-label">Default Country:</label>
				<div class="col-sm-8">
					<input type="text" name="default_country" value="<?= get_config($dbc, 'default_country') ?>" class="form-control">
				</div>
				<label class="col-sm-4 control-label">Default Province:</label>
				<div class="col-sm-8">
					<input type="text" name="default_province" value="<?= get_config($dbc, 'default_province') ?>" class="form-control">
				</div>
				<label class="col-sm-4 control-label">Provinces:</label>
				<div class="col-sm-8">
					<script>
					function add_province() {
						var clone = $('.province_line').last().clone();
						clone.find('input').val('');
						$('.province_line').last().after(clone);
					}
					</script>
					<div class="col-sm-4 text-center">Province</div><div class="col-sm-2 text-center"><?= $gst_name ?> Rate</div><div class="col-sm-2 text-center"><?= $pst_name ?> Rate</div><div class="col-sm-2 text-center"><?= $hst_name ?> Rate</div>
					<?php $province_list = explode('#*#',get_config($dbc, 'expense_provinces'));
					foreach($province_list as $province_data) {
						$data = explode('*',$province_data); ?>
						<div class="province_line">
							<div class="col-sm-4"><input type="text" name="province_code[]" value="<?= $data[0] ?>" class="form-control"></div>
							<div class="col-sm-2"><input type="text" name="province_gst[]" value="<?= $data[1] ?>" class="form-control"></div>
							<div class="col-sm-2"><input type="text" name="province_pst[]" value="<?= $data[2] ?>" class="form-control"></div>
							<div class="col-sm-2"><input type="text" name="province_hst[]" value="<?= $data[3] ?>" class="form-control"></div>
							<div class="col-sm-2"><button class="btn brand-btn" onclick="$(this).closest('.province_line').remove();">Delete</button></div>
						</div>
					<?php } ?>
					<button class="btn brand-btn pull-right" onclick="add_province(); return false;">Add Province</button>
				</div>
				<h3>Expense Reminder</h3>
				<div class="form-group">
					<label class="col-sm-4 control-label">Days Before End of Month:<br /><em>To disable reminders, set this field to 0.<br />Any higher number will send a reminder that many days before the last day of the month.</em></label>
					<div class="col-sm-8">
						<input type="number" name="expense_reminder_days" value="<?= get_config($dbc, 'expense_reminder_days') ?>" class="form-control" min=0>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Email Sender:</label>
					<div class="col-sm-8">
						<input type="text" name="expense_reminder_sender" value="<?= get_config($dbc, 'expense_reminder_sender') ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Email Subject:</label>
					<div class="col-sm-8">
						<input type="text" name="expense_reminder_subject" value="<?= get_config($dbc, 'expense_reminder_subject') ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Email Body:</label>
					<div class="col-sm-8">
						<textarea name="expense_reminder_body" class="form-control"><?= get_config($dbc, 'expense_reminder_body') ?></textarea>
					</div>
				</div>
				<h3>Exchange Rates</h3>
				<p><em>Exchange Rates are loaded from the Bank of Canada when the Expense is entered, and uses the exchange rate for the expense date, if available. The rates are available from
					2017-01-02. All Bank of Canada exchange rates are indicative rates only, obtained from averages of aggregated price quotes from financial institutions. You can read their full
					<a href="https://www.bankofcanada.ca/terms/#fx-rates">Terms and Conditions</a> for details.</em></p>
				<div class="form-group">
					<label class="col-sm-4 control-label">Exchange Rate Buffer Allowance:<br /><em>Exchange rates listed are the average exchange rate for the day. When exchanging currency, there
						is an additional amount that is typically charged, often referred to as a currency conversion fee of two to three percent. If you wish to automatically add a percentage
						allowance to cover this fee, enter the percent here.</em></label>
					<div class="col-sm-8">
						<input type="number" name="exchange_buffer" step="any" value="<?= $exchange_buffer * 100 ?>" class="form-control" min=0>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php if($tab != 'budget' && $tab != 'manager' && $tab != 'payables' && $tab != 'report'): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
						Choose Fields for Expense<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_field" class="panel-collapse collapse">
				<div class="panel-body">
					<div id='no-more-tables'>
					<h3>Default Expense Types</h3>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Flight".',') !== FALSE) { echo " checked"; } ?> value="Flight" style="height: 20px; width: 20px;" name="expense[]"> Flight</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Hotel".',') !== FALSE) { echo " checked"; } ?> value="Hotel" style="height: 20px; width: 20px;" name="expense[]"> Hotel</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Breakfast".',') !== FALSE) { echo " checked"; } ?> value="Breakfast" style="height: 20px; width: 20px;" name="expense[]"> Breakfast</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Lunch".',') !== FALSE) { echo " checked"; } ?> value="Lunch" style="height: 20px; width: 20px;" name="expense[]"> Lunch</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Dinner".',') !== FALSE) { echo " checked"; } ?> value="Dinner" style="height: 20px; width: 20px;" name="expense[]"> Dinner</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Beverages".',') !== FALSE) { echo " checked"; } ?> value="Beverages" style="height: 20px; width: 20px;" name="expense[]"> Beverages</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Transportation".',') !== FALSE) { echo " checked"; } ?> value="Transportation" style="height: 20px; width: 20px;" name="expense[]"> Transportation</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Entertainment".',') !== FALSE) { echo " checked"; } ?> value="Entertainment" style="height: 20px; width: 20px;" name="expense[]"> Entertainment</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Gas".',') !== FALSE) { echo " checked"; } ?> value="Gas" style="height: 20px; width: 20px;" name="expense[]"> Gas</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Misc".',') !== FALSE) { echo " checked"; } ?> value="Misc" style="height: 20px; width: 20px;" name="expense[]"> Misc</label>
					</div>
					  <div class="form-group">
						<label for="office_zip" class="col-sm-4 control-label">Empty Expense Rows To Display Per Category:</label>
						<div class="col-sm-8">
						  <input name="expense_rows" value="<?php echo $expense_rows; ?>" type="text" class="form-control office_zip" />
						</div>
					  </div>
					  <div class="form-group">
						<label for="office_zip" class="col-sm-4 control-label">Additional Types<br><em>(separated by a comma, no spaces)</em>:</label>
						<div class="col-sm-8">
						  <input name="expense_types" value="<?php echo $expense_types; ?>" type="text" class="form-control office_zip" />
						</div>
					  </div>

					  <div class="form-group">
						<label for="office_zip" class="col-sm-4 control-label">Tax Name:<br><em>(e.g. - GST, HST, etc.)</em></label>
						<div class="col-sm-8">
						  <input name="gst_name" value="<?php echo $gst_name; ?>" type="text" class="form-control" />
						</div>
					  </div>
					  <div class="form-group">
						<label for="office_zip" class="col-sm-4 control-label">Tax Amount %:<br><em>(e.g. - 5)</em></label>
						<div class="col-sm-8">
						  <input name="gst_amt" value="<?php echo $gst_amt; ?>" type="text" class="form-control" />
						</div>
					  </div>
					  <div class="form-group">
						<label for="office_zip" class="col-sm-4 control-label">Additional Tax Name:<br><em>(e.g. - HST, PST, Sales Tax, etc.)</em></label>
						<div class="col-sm-8">
						  <input name="pst_name" value="<?php echo $pst_name; ?>" type="text" class="form-control" />
						</div>
					  </div>
					  <div class="form-group">
						<label for="office_zip" class="col-sm-4 control-label">Additional Tax Amount %:<br><em>(e.g. - 5)</em></label>
						<div class="col-sm-8">
						  <input name="pst_amt" value="<?php echo $pst_amt; ?>" type="text" class="form-control" />
						</div>
					  </div>
					  <div class="form-group">
						<label for="office_zip" class="col-sm-4 control-label">Additional Tax Name:<br><em>(e.g. - HST, PST, Sales Tax, etc.)</em></label>
						<div class="col-sm-8">
						  <input name="hst_name" value="<?php echo $hst_name; ?>" type="text" class="form-control" />
						</div>
					  </div>
					  <div class="form-group">
						<label for="office_zip" class="col-sm-4 control-label">Additional Tax Amount %:<br><em>(e.g. - 5)</em></label>
						<div class="col-sm-8">
						  <input name="hst_amt" value="<?php echo $hst_amt; ?>" type="text" class="form-control" />
						</div>
					  </div>
				</div>
			</div>
		</div>
<?php endif; ?>
<?php if($tab != 'budget'): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field2" >
						Choose Fields for Expense Dashboard<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_field2" class="panel-collapse collapse">
				<div class="panel-body">
					<div id='no-more-tables'>
					Move the fields around to change the display order.
					<div class='sortable' style='border:solid 1px black;'>
						<?php $db_config = explode(',',trim($db_config,','));
						$db_config_arr = array_filter(array_unique(array_merge($db_config,explode(',','Contact,Expense For,Description,Country,Province,Exchange,Date,Work Order,Receipt,Type,Day Expense,Amount,Tips,Local Tax,Tax,Third Tax,Total,Budget,Reimburse'))));
						foreach($db_config_arr as $field) {
							if($field == 'Contact') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Expense Contact'.'</label>';
							} else if($field == 'Expense For') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Expense Tab'.'</label>';
							} else if($field == 'Description') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Description'.'</label>';
							} else if($field == 'Country') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Country of Expense'.'</label>';
							} else if($field == 'Province') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Province of Expense'.'</label>';
							} else if($field == 'Exchange') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Exchange Currency'.'</label>';
							} else if($field == 'Date') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Expense Date'.'</label>';
							} else if($field == 'Work Order') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Work Order #'.'</label>';
							} else if($field == 'Receipt') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Receipt'.'</label>';
							} else if($field == 'Type') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Expense Type'.'</label>';
							} else if($field == 'Day Expense') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Day Expense'.'</label>';
							} else if($field == 'Amount') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]" readonly> '.'Amount'.'</label>';
							} else if($field == 'Tips') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Tips'.'</label>';
							} else if($field == 'Third Tax') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.($hst_name == '' ? 'Third Tax' : $hst_name).'</label>';
							} else if($field == 'Local Tax') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.($pst_name == '' ? 'Additional Tax' : $pst_name).'</label>';
							} else if($field == 'Tax') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.($gst_name == '' ? 'Tax' : $gst_name).'</label>';
							} else if($field == 'Total') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Total'.'</label>';
							} else if($field == 'Budget') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Budget'.'</label>';
							} else if($field == 'Reimburse') {
								echo '<label><input type="checkbox" '.(in_array($field,$db_config) ? 'checked' : '').' value="'.$field.'" name="expense_dashboard[]"> '.'Reimburse'.'</label>';
							}
						}
						?>
					</div>
				   </div>
				</div>
			</div>
		</div>
<?php endif; ?>
<?php if($tab != 'budget' && $tab != 'manager' && $tab != 'payables' && $tab != 'report'): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field11" >
						Logo, Header & Footer<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_field11" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group">
					<label for="file[]" class="col-sm-4 control-label">Header Logo
					<span class="popover-examples list-inline">&nbsp;
					<a  data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
					</span>
					:</label>
					<div class="col-sm-8">
					<?php
						$logo = get_config($dbc, 'expense_logo');
						if($logo != '') {
						echo '<a href="download/'.$logo.'" target="_blank">View</a>';
						?>
						<input type="hidden" name="logo_file" value="<?php echo $logo; ?>" />
						<input name="logo" type="file" data-filename-placement="inside" class="form-control" />
					  <?php } else { ?>
					  <input name="logo" type="file" data-filename-placement="inside" class="form-control" />
					  <?php } ?>
					</div>
					</div>

					<div class="form-group">
						<label for="office_country" class="col-sm-4 control-label">Header Info:<br><em>(e.g. - company name, address, phone, etc.)</em></label>
						<div class="col-sm-8">
							<textarea name="expense_header" rows="3" cols="50" class="form-control"><?php echo get_config($dbc, 'expense_header'); ?></textarea>
						</div>
					</div>

					<div class="form-group">
						<label for="office_country" class="col-sm-4 control-label">Footer Info:<br><em>(e.g. - company name, address, phone, etc.)</em></label>
						<div class="col-sm-8">
							<textarea name="expense_footer" rows="3" cols="50" class="form-control"><?php echo get_config($dbc, 'expense_footer'); ?></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_categories" >
						Expense Categories and Headings<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_categories" class="panel-collapse collapse">
				<div class="panel-body">
					<?php if($tab == 'business' || $tab == 'customers' || $tab == 'clients' || $tab == 'staff' || $tab == 'sales'): ?>
						<table class="table table-bordered" id="category_table">
							<tr class="hidden-xs hidden-sm">
								<th>Category</th>
								<th>Heading</th>
								<th>Read Only Amount</th>
								<th>Monthly</th>
								<th>Q1</th>
								<th>Q2</th>
								<th>Q3</th>
								<th>Q4</th>
								<th>Annual</th>
								<th></th>
							</tr>
							<?php $categories_result = mysqli_query($dbc, "SELECT * FROM (SELECT `categoryid`, `category`, `heading`, `amount`, `monthly`, `q1`, `q2`, `q3`, `q4`, `annual` FROM `expense_categories` WHERE `expense_tab`='$tab' AND `deleted`=0 ORDER BY `EC`, `GL`) `categories`
								UNION SELECT '', '', '', '', '', '', '', '', '', ''");
							while($row = mysqli_fetch_array($categories_result)) { ?>
								<tr>
									<input type="hidden" name="cat_id[]" value="<?php echo $row['categoryid']; ?>">
									<td data-title="Category"><input type="text" name="category[]" class="form-control" value="<?php echo $row['category']; ?>"></td>
									<td data-title="Heading"><input type="text" name="cat_heading[]" class="form-control" value="<?php echo $row['heading']; ?>"></td>
									<td data-title="Read Only Amount"><input type="text" name="cat_amount[]" class="form-control" value="<?php echo $row['amount']; ?>"></td>
									<td data-title="Monthly Budget"><input type="text" name="cat_monthly[]" class="form-control" value="<?php echo $row['monthly']; ?>"></td>
									<td data-title="Q1 Budget"><input type="text" name="cat_q1[]" class="form-control" value="<?php echo $row['q1']; ?>"></td>
									<td data-title="Q2 Budget"><input type="text" name="cat_q2[]" class="form-control" value="<?php echo $row['q2']; ?>"></td>
									<td data-title="Q3 Budget"><input type="text" name="cat_q3[]" class="form-control" value="<?php echo $row['q3']; ?>"></td>
									<td data-title="Q4 Budget"><input type="text" name="cat_q4[]" class="form-control" value="<?php echo $row['q4']; ?>"></td>
									<td data-title="Annual Budget"><input type="text" name="cat_annual[]" class="form-control" value="<?php echo $row['annual']; ?>"></td>
									<td><a href="" onclick="$(this).closest('tr').remove(); return false;">Delete</a></td>
								</tr>
							<?php } ?>
						</table>
						<button onclick="add_category(); return false;" class="btn brand-btn pull-right">Add Category</button>
					<?php elseif($tab == 'current_month'): ?>
						The categories will draw from the Expense tabs. Depending on which tab is selected, the appropriate categories will be displayed.
					<?php endif; ?>
				</div>
			</div>
		</div>
		<script>
		function add_category() {
			var table = $('#category_table');
			var new_row = table.find('td:last').closest('tr').clone();
			new_row.find('input').val('');
			table.append(new_row);
			$('[name="category[]"]').last().focus();
		}
		</script>
	<?php endif; ?>
</div>

<div class="form-group">
    <div class="col-sm-6">
        <a href="expenses.php?tab=<?php echo $tab; ?>" class="btn brand-btn btn-lg">Back</a>
		<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
    </div>
    <div class="col-sm-6">
        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
    </div>
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>
