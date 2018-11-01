<?php
include ('../include.php');
checkAuthorised('field_job');
error_reporting(0);

$current_tab = (isset($_GET['tab']) ? $_GET['tab'] : 'sites');
switch($current_tab) {
	case 'sites': $url = "field_sites.php"; break;
	case 'jobs': $url = "field_jobs.php"; break;
	case 'foreman': $url = "field_foreman_sheet.php"; break;
	case 'po': $url = "field_po.php"; break;
	case 'work': $url = "field_work_ticket.php"; break;
	case 'invoice': $url = "field_invoice.php"; break;
	case 'payroll': $url = "field_payroll.php"; break;
	default: $url = "field_sites.php"; break;
}

if (isset($_POST['submit_tabs'])) {

	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	$tab_list = implode(',', $_POST['tab_field_jobs']);

	$result = mysqli_fetch_array(mysqli_query($dbc, "select count(*) rows from general_configuration where name='field_job_tabs'"));
	if($result['rows'] > 0) {
		$sql_tabs = "update general_configuration set value=',$tab_list,' where name='field_job_tabs'";
	}
	else {
		$sql_tabs = "insert into general_configuration (name, value) VALUES ('field_job_tabs', ',$tab_list,')";
	}
	mysqli_query($dbc, $sql_tabs);
    $message .= "<script></script>";
}

if (isset($_POST['submit_sites'])) {

    $dashboard_list = implode(',',$_POST['dashboard_site']);
    $field_list = implode(',',$_POST['field_site']);

	$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT count(*) AS rows FROM field_config_field_jobs WHERE tab='sites'"));

	if($result['rows'] > 0) {
        $sql_fields = "UPDATE `field_config_field_jobs` SET `field_list` = ',$field_list,', `dashboard_list` = ',$dashboard_list,' WHERE tab='sites'";
	} else {
		$sql_fields = "INSERT INTO field_config_field_jobs (`tab`, `field_list`, `dashboard_list`) VALUES ('sites', ',$field_list,', ',$dashboard_list,')";
	}
	mysqli_query($dbc, $sql_fields);

	$before_change = '';
  $history = "field_config_field_jobs entry has been updated for field_list -> $field_list. <br />";
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
    $message .= "<script></script>";
}

if (isset($_POST['submit_jobs'])) {

    $dashboard_list = implode(',',$_POST['dashboard_job']);
    $field_list = implode(',',$_POST['field_job']);

	$result = mysqli_fetch_array(mysqli_query($dbc, "select count(*) rows from field_config_field_jobs where tab='jobs'"));
	if($result['rows'] > 0) {
		$sql_fields = "update field_config_field_jobs set field_list=',$field_list,', dashboard_list=',$dashboard_list,' where tab='jobs'";
	} else {
		$sql_fields = "insert into field_config_field_jobs (tab, field_list, dashboard_list) VALUES ('jobs', ',$field_list,', ',$dashboard_list,')";
	}

	mysqli_query($dbc, $sql_fields);
    $message .= "<script></script>";
}

if (isset($_POST['submit_foreman'])) {
    $dashboard_list = implode(',',$_POST['dashboard_foreman']);
    $field_list = implode(',',$_POST['field_foreman']);

	$result = mysqli_fetch_array(mysqli_query($dbc, "select count(*) rows from field_config_field_jobs where tab='foreman'"));
	if($result['rows'] > 0) {
		$sql_fields = "update field_config_field_jobs set field_list=',$field_list,', dashboard_list=',$dashboard_list,' where tab='foreman'";
	} else {
		$sql_fields = "insert into field_config_field_jobs (tab, field_list, dashboard_list) VALUES ('foreman', ',$field_list,', ',$dashboard_list,')";
	}

    $fs_supervisor_email = filter_var($_POST['fs_supervisor_email'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='fs_supervisor_email'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$fs_supervisor_email' WHERE name='fs_supervisor_email'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('fs_supervisor_email', '$fs_supervisor_email')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $fs_from_email = filter_var($_POST['fs_from_email'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='fs_from_email'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$fs_from_email' WHERE name='fs_from_email'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('fs_from_email', '$fs_from_email')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $fs_approval_email = filter_var($_POST['fs_approval_email'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='fs_approval_email'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$fs_approval_email' WHERE name='fs_approval_email'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('fs_approval_email', '$fs_approval_email')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	mysqli_query($dbc, $sql_fields);
    $message .= "<script></script>";
}

if (isset($_POST['submit_po'])) {
    $dashboard_list = implode(',',$_POST['dashboard_po']);
    $field_list = implode(',',$_POST['field_po']);

	$result = mysqli_fetch_array(mysqli_query($dbc, "select count(*) rows from field_config_field_jobs where tab='po'"));
	if($result['rows'] > 0) {
		$sql_fields = "update field_config_field_jobs set field_list=',$field_list,', dashboard_list=',$dashboard_list,' where tab='po'";
	} else {
		$sql_fields = "insert into field_config_field_jobs (tab, field_list, dashboard_list) VALUES ('po', ',$field_list,', ',$dashboard_list,')";
	}
	mysqli_query($dbc, $sql_fields);

    $field_jobs_po_logo = $_FILES["field_jobs_po_logo"]["name"];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='field_jobs_po_logo'"));
    if($get_config['configid'] > 0) {
		if($field_jobs_po_logo == '') {
			$logo_update = $_POST['logo_file'];
		} else {
			$logo_update = $field_jobs_po_logo;
		}
		move_uploaded_file($_FILES["field_jobs_po_logo"]["tmp_name"],"download/" . $logo_update);
        $query_update_employee = "UPDATE `general_configuration` SET value = '$logo_update' WHERE name='field_jobs_po_logo'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
		move_uploaded_file($_FILES["field_jobs_po_logo"]["tmp_name"], "download/" . $_FILES["field_jobs_po_logo"]["name"]) ;

        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('field_jobs_po_logo', '$field_jobs_po_logo')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $survey = htmlentities($_POST['field_jobs_po_address']);
    $field_jobs_po_address = filter_var($survey,FILTER_SANITIZE_STRING);

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='field_jobs_po_address'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$field_jobs_po_address' WHERE name='field_jobs_po_address'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('field_jobs_po_address', '$field_jobs_po_address')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    $message .= "<script></script>";
}

if (isset($_POST['submit_work'])) {
    $dashboard_list = implode(',',$_POST['dashboard_work']);
    $field_list = implode(',',$_POST['field_work']);

	$result = mysqli_fetch_array(mysqli_query($dbc, "select count(*) rows from field_config_field_jobs where tab='work'"));
	if($result['rows'] > 0) {
		$sql_fields = "update field_config_field_jobs set field_list=',$field_list,', dashboard_list=',$dashboard_list,' where tab='work'";
	} else {
		$sql_fields = "insert into field_config_field_jobs (tab, field_list, dashboard_list) VALUES ('work', ',$field_list,', ',$dashboard_list,')";
	}
	mysqli_query($dbc, $sql_fields);

    $field_jobs_wt_logo = $_FILES["field_jobs_wt_logo"]["name"];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='field_jobs_wt_logo'"));
    if($get_config['configid'] > 0) {
		if($field_jobs_wt_logo == '') {
			$logo_update = $_POST['logo_file_wt'];
		} else {
			$logo_update = $field_jobs_wt_logo;
		}
		move_uploaded_file($_FILES["field_jobs_wt_logo"]["tmp_name"],"download/" . $logo_update);
        $query_update_employee = "UPDATE `general_configuration` SET value = '$logo_update' WHERE name='field_jobs_wt_logo'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
		move_uploaded_file($_FILES["field_jobs_wt_logo"]["tmp_name"], "download/" . $_FILES["field_jobs_wt_logo"]["name"]) ;

        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('field_jobs_wt_logo', '$field_jobs_wt_logo')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $survey = htmlentities($_POST['field_jobs_wt_address']);
    $field_jobs_wt_address = filter_var($survey,FILTER_SANITIZE_STRING);

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='field_jobs_wt_address'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$field_jobs_wt_address' WHERE name='field_jobs_wt_address'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('field_jobs_wt_address', '$field_jobs_wt_address')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $wt_bcc_email = filter_var($_POST['wt_bcc_email'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='wt_bcc_email'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$wt_bcc_email' WHERE name='wt_bcc_email'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('wt_bcc_email', '$wt_bcc_email')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    $wt_email_from = filter_var($_POST['wt_email_from'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='wt_email_from'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$wt_email_from' WHERE name='wt_email_from'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('wt_email_from', '$wt_email_from')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    $wt_email_subject = filter_var($_POST['wt_email_subject'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='wt_email_subject'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$wt_email_subject' WHERE name='wt_email_subject'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('wt_email_subject', '$wt_email_subject')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    $wt_email_body = filter_var(htmlentities($_POST['wt_email_body']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='wt_email_body'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$wt_email_body' WHERE name='wt_email_body'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('wt_email_body', '$wt_email_body')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $message .= "<script></script>";
}

if (isset($_POST['submit_invoice'])) {
    $dashboard_list = implode(',',$_POST['dashboard_invoice']);
    $field_list = implode(',',$_POST['field_invoice']);

	$result = mysqli_fetch_array(mysqli_query($dbc, "select count(*) rows from field_config_field_jobs where tab='invoice'"));
	if($result['rows'] > 0) {
		$sql_fields = "update field_config_field_jobs set field_list=',$field_list,', dashboard_list=',$dashboard_list,' where tab='invoice'";
	} else {
		$sql_fields = "insert into field_config_field_jobs (tab, field_list, dashboard_list) VALUES ('invoice', ',$field_list,', ',$dashboard_list,')";
	}
	mysqli_query($dbc, $sql_fields);

    $field_jobs_invoice_logo = $_FILES["field_jobs_invoice_logo"]["name"];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='field_jobs_invoice_logo'"));
    if($get_config['configid'] > 0) {
		if($field_jobs_invoice_logo == '') {
			$logo_update = $_POST['logo_file_invoice'];
		} else {
			$logo_update = $field_jobs_invoice_logo;
		}
		move_uploaded_file($_FILES["field_jobs_invoice_logo"]["tmp_name"],"download/" . $logo_update);
        $query_update_employee = "UPDATE `general_configuration` SET value = '$logo_update' WHERE name='field_jobs_invoice_logo'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
		move_uploaded_file($_FILES["field_jobs_invoice_logo"]["tmp_name"], "download/" . $_FILES["field_jobs_invoice_logo"]["name"]) ;

        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('field_jobs_invoice_logo', '$field_jobs_invoice_logo')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $survey = htmlentities($_POST['field_jobs_invoice_address']);
    $field_jobs_invoice_address = filter_var($survey,FILTER_SANITIZE_STRING);

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='field_jobs_invoice_address'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$field_jobs_invoice_address' WHERE name='field_jobs_invoice_address'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('field_jobs_invoice_address', '$field_jobs_invoice_address')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    $message .= "<script></script>";
}

if (isset($_POST['submit_payroll'])) {
    $dashboard_list = implode(',',$_POST['dashboard_payroll']);
    $field_list = implode(',',$_POST['field_payroll']);

	$result = mysqli_fetch_array(mysqli_query($dbc, "select count(*) rows from field_config_field_jobs where tab='payroll'"));
	if($result['rows'] > 0) {
		$sql_fields = "update field_config_field_jobs set field_list=',$field_list,', dashboard_list=',$dashboard_list,' where tab='payroll'";
	} else {
		$sql_fields = "insert into field_config_field_jobs (tab, field_list, dashboard_list) VALUES ('payroll', ',$field_list,', ',$dashboard_list,')";
	}
	mysqli_query($dbc, $sql_fields);
    $message .= "<script></script>";
}

// Get Database Data
$sql = "select field_list, dashboard_list from field_config_field_jobs where tab='$current_tab'";
$result = mysqli_fetch_array(mysqli_query($dbc, $sql));
$field_list = $result['field_list'];
$dashboard_list = $result['dashboard_list'];
$sql = "select value from general_configuration where name='field_job_tabs'";
$result = mysqli_fetch_array(mysqli_query($dbc, $sql));
$tab_list = $result['value'];
?>
<script type="text/javascript">
$(document).ready(function() {
    $(".selecctall").change(function(){
      $("input:checkbox").prop('checked', $(this).prop("checked"));
    });
});
</script>
</head>
<body>
<?php include ('../navigation.php'); ?>

<div class="container">
	<div class="row">
		<h1>Field Jobs</h1>
		<div class="gap-top double-gap-bottom">
			<a href="<?php echo $url; ?>" class="btn config-btn">Back to Dashboard</a> <?php echo $message; ?>
			<input type="checkbox" class="selecctall"/> Select All
        </div>

		<div class="tab-container mobile-100-container">
			<a href='config_field_jobs.php?tab=manage'><button type='button' class='btn brand-btn mobile-block<?php echo ('manage' == $current_tab ? ' active_tab' : ''); ?>' >Tabs</button></a>
            <a href='config_field_jobs.php?tab=sites'><button type='button' class='btn brand-btn mobile-block<?php echo ('sites' == $current_tab ? ' active_tab' : ''); ?>' >Sites</button></a>
			<a href='config_field_jobs.php?tab=jobs'><button type='button' class='btn brand-btn mobile-block<?php echo ('jobs' == $current_tab ? ' active_tab' : ''); ?>' >Jobs</button></a>
			<a href='config_field_jobs.php?tab=foreman'><button type='button' class='btn brand-btn mobile-block<?php echo ('foreman' == $current_tab ? ' active_tab' : ''); ?>' >Foreman Sheet</button></a>
			<a href='config_field_jobs.php?tab=po'><button type='button' class='btn brand-btn mobile-block<?php echo ('po' == $current_tab ? ' active_tab' : ''); ?>' >PO</button></a>
			<a href='config_field_jobs.php?tab=work'><button type='button' class='btn brand-btn mobile-block<?php echo ('work' == $current_tab ? ' active_tab' : ''); ?>' >Work Ticket</button></a>
			<a href='config_field_jobs.php?tab=invoice'><button type='button' class='btn brand-btn mobile-block<?php echo ('invoice' == $current_tab ? ' active_tab' : ''); ?>' >Invoice</button></a>
			<a href='config_field_jobs.php?tab=payroll'><button type='button' class='btn brand-btn mobile-block<?php echo ('payroll' == $current_tab ? ' active_tab' : ''); ?>' >Payroll</button></a>
		</div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
			<div class="panel-group" id="accordion2">
				<div class="clearfix"></div>
				<div class="panel-group" id="accordion_config">

					<?php if($current_tab == 'manage'): ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_tabs">
									Manage Tabs<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>
						<div id="collapse_tabs" class="panel-collapse collapse">
							<div class="panel-body">
								<div class="no-more-tables">
									<label><input type="checkbox" value="sites"<?php echo (strpos($tab_list,',jobs,') !== false ? " checked" : ""); ?>
									name="tab_field_jobs[]" style="margin:0.5em;">Sites</label>
									<label><input type="checkbox" value="jobs"<?php echo (strpos($tab_list,',jobs,') !== false ? " checked" : ""); ?>
									name="tab_field_jobs[]" style="margin:0.5em;">Jobs</label>
									<label><input type="checkbox" value="foreman"<?php echo (strpos($tab_list,',foreman,') !== false ? " checked" : ""); ?>
									name="tab_field_jobs[]" style="margin:0.5em;">Foreman Sheet</label>
									<label><input type="checkbox" value="po"<?php echo (strpos($tab_list,',po,') !== false ? " checked" : ""); ?>
									name="tab_field_jobs[]" style="margin:0.5em;">PO</label>
									<label><input type="checkbox" value="work"<?php echo (strpos($tab_list,',work,') !== false ? " checked" : ""); ?>
									name="tab_field_jobs[]" style="margin:0.5em;">Work Ticket</label>
									<label><input type="checkbox" value="invoice"<?php echo (strpos($tab_list,',invoice,') !== false ? " checked" : ""); ?>
									name="tab_field_jobs[]" style="margin:0.5em;">Invoice</label>
									<label><input type="checkbox" value="payroll"<?php echo (strpos($tab_list,',payroll,') !== false ? " checked" : ""); ?>
									name="tab_field_jobs[]" style="margin:0.5em;">Payroll</label>
								</div>
							</div>
						</div>
					</div>
					<?php endif; ?>

					<?php if($current_tab == 'sites'):
						if(str_replace(',','',$dashboard_list) == '') {
							$dashboard_list = ',site_name,';
						}
						if(str_replace(',','',$field_list) == '') {
							$field_list = ',customer,site_name,website,display,phone,fax,photo,description,office_address,office_city,office_province,office_country,office_postal,address_sync,mail_address,mail_country,mail_city,mail_province,mail_postal,';
						} ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_sites_dashboard">
										Sites Dashboard<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_sites_dashboard" class="panel-collapse collapse">
								<div class="panel-body">
									<div class="no-more-tables">
										<label><input type="checkbox" value="site_name"<?php echo (strpos($dashboard_list,',site_name,') !== false ? " checked" : ""); ?>
									    name="dashboard_site[]" style="margin:0.5em;">Site Name</label>
										<label><input type="checkbox" value="customer"<?php echo (strpos($dashboard_list,',customer,') !== false ? " checked" : ""); ?>
										name="dashboard_site[]" style="margin:0.5em;">Customer</label>
										<label><input type="checkbox" value="website"<?php echo (strpos($dashboard_list,',website,') !== false ? " checked" : ""); ?>
										name="dashboard_site[]" style="margin:0.5em;">Website</label>
										<label><input type="checkbox" value="display"<?php echo (strpos($dashboard_list,',display,') !== false ? " checked" : ""); ?>
										name="dashboard_site[]" style="margin:0.5em;">Display Name</label>
										<label><input type="checkbox" value="address"<?php echo (strpos($dashboard_list,',address,') !== false ? " checked" : ""); ?>
										name="dashboard_site[]" style="margin:0.5em;">Full Address</label>
										<label><input type="checkbox" value="phone"<?php echo (strpos($dashboard_list,',phone,') !== false ? " checked" : ""); ?>
										name="dashboard_site[]" style="margin:0.5em;">Phone Number</label>
										<label><input type="checkbox" value="fax"<?php echo (strpos($dashboard_list,',fax,') !== false ? " checked" : ""); ?>
										name="dashboard_site[]" style="margin:0.5em;">Fax Number</label>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_site_fields">
										Site Fields<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_site_fields" class="panel-collapse collapse">
								<div class="panel-body">
									<div class="no-more-tables">
										<label><input type="checkbox" value="customer"<?php echo (strpos($field_list,',customer,') !== false ? " checked" : ""); ?>
									    name="field_site[]" style="margin:0.5em;">Customer</label>
										<label><input type="checkbox" value="site_name"<?php echo (strpos($field_list,',site_name,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Site Name (Location</label>
										<label><input type="checkbox" value="website"<?php echo (strpos($field_list,',website,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Website</label>

										<label><input type="checkbox" value="display"<?php echo (strpos($field_list,',display,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Display Name</label>

										<label><input type="checkbox" value="phone"<?php echo (strpos($field_list,',phone,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Phone Number</label>

										<label><input type="checkbox" value="fax"<?php echo (strpos($field_list,',fax,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Fax Number</label>

										<label><input type="checkbox" value="photo"<?php echo (strpos($field_list,',photo,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Upload Photo</label>

										<label><input type="checkbox" value="description"<?php echo (strpos($field_list,',description,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Description</label>

										<label><input type="checkbox" value="office_address"<?php echo (strpos($field_list,',office_address,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Office Address</label>

										<label><input type="checkbox" value="office_city"<?php echo (strpos($field_list,',office_city,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Office City</label>

										<label><input type="checkbox" value="office_province"<?php echo (strpos($field_list,',office_province,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Office Province</label>

										<label><input type="checkbox" value="office_country"<?php echo (strpos($field_list,',office_country,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Office Country</label>

										<label><input type="checkbox" value="office_postal"<?php echo (strpos($field_list,',office_postal,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Office Postal Code</label>

										<label><input type="checkbox" value="address_sync"<?php echo (strpos($field_list,',address_sync,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Sync Office and Mailing Address</label>

										<label><input type="checkbox" value="mail_address"<?php echo (strpos($field_list,',mail_address,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Mailing Address</label>

										<label><input type="checkbox" value="mail_country"<?php echo (strpos($field_list,',mail_country,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Mailing Country</label>

										<label><input type="checkbox" value="mail_city"<?php echo (strpos($field_list,',mail_city,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Mailing City</label>

										<label><input type="checkbox" value="mail_province"<?php echo (strpos($field_list,',mail_province,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Mailing Province</label>

										<label><input type="checkbox" value="mail_postal"<?php echo (strpos($field_list,',mail_postal,') !== false ? " checked" : ""); ?>
										name="field_site[]" style="margin:0.5em;">Mailing Postal Code</label>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<?php if($current_tab == 'jobs'):
						if(str_replace(',','',$dashboard_list) == '') {
							$dashboard_list = ',job,contact,foreman,';
						}
						if(str_replace(',','',$field_list) == '') {
							$field_list = ',date,job,contact,rate,foreman,afe,additional,location,overview,';
						} ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_jobs_dashboard">
										Jobs Dashboard<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_jobs_dashboard" class="panel-collapse collapse">
								<div class="panel-body">
									<div class="no-more-tables">
										<label><input type="checkbox" value="job"<?php echo (strpos($dashboard_list,',job,') !== false ? " checked" : ""); ?>
										name="dashboard_job[]" style="margin:0.5em;">Job #</label>

										<label><input type="checkbox" value="contact"<?php echo (strpos($dashboard_list,',contact,') !== false ? " checked" : ""); ?>
										name="dashboard_job[]" style="margin:0.5em;">Contact</label>

										<label><input type="checkbox" value="site"<?php echo (strpos($dashboard_list,',site,') !== false ? " checked" : ""); ?>
										name="dashboard_job[]" style="margin:0.5em;">Site Location</label>

										<label><input type="checkbox" value="foreman"<?php echo (strpos($dashboard_list,',foreman,') !== false ? " checked" : ""); ?>
										name="dashboard_job[]" style="margin:0.5em;">Foreman</label>

										<label><input type="checkbox" value="foreman_sheet"<?php echo (strpos($dashboard_list,',foreman_sheet,') !== false ? " checked" : ""); ?>
										name="dashboard_job[]" style="margin:0.5em;">Foreman Sheet</label>

										<label><input type="checkbox" value="work_ticket"<?php echo (strpos($dashboard_list,',work_ticket,') !== false ? " checked" : ""); ?>
										name="dashboard_job[]" style="margin:0.5em;">Work Ticket</label>

										<label><input type="checkbox" value="po"<?php echo (strpos($dashboard_list,',po,') !== false ? " checked" : ""); ?>
										name="dashboard_job[]" style="margin:0.5em;">PO</label>

										<label><input type="checkbox" value="invoice"<?php echo (strpos($dashboard_list,',invoice,') !== false ? " checked" : ""); ?>
										name="dashboard_job[]" style="margin:0.5em;">Invoice</label>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_job_fields">
										Job Fields<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_job_fields" class="panel-collapse collapse">
								<div class="panel-body">
									<div class="no-more-tables">
										<label><input type="checkbox" value="date"<?php echo (strpos($field_list,',date,') !== false ? " checked" : ""); ?>
										name="field_job[]" style="margin:0.5em;">Job Date</label>

										<label><input type="checkbox" value="job"<?php echo (strpos($field_list,',job,') !== false ? " checked" : ""); ?>
										name="field_job[]" style="margin:0.5em;">Job #</label>

										<label><input type="checkbox" value="contact"<?php echo (strpos($field_list,',contact,') !== false ? " checked" : ""); ?>
										name="field_job[]" style="margin:0.5em;">Contact</label>

										<label><input type="checkbox" value="rate"<?php echo (strpos($field_list,',rate,') !== false ? " checked" : ""); ?>
										name="field_job[]" style="margin:0.5em;">Rate Card</label>

										<label><input type="checkbox" value="foreman"<?php echo (strpos($field_list,',foreman,') !== false ? " checked" : ""); ?>
										name="field_job[]" style="margin:0.5em;">Foreman</label>

										<label><input type="checkbox" value="afe"<?php echo (strpos($field_list,',afe,') !== false ? " checked" : ""); ?>
										name="field_job[]" style="margin:0.5em;">AFE #</label>

										<label><input type="checkbox" value="additional"<?php echo (strpos($field_list,',additional,') !== false ? " checked" : ""); ?>
										name="field_job[]" style="margin:0.5em;">Additional Info</label>

										<label><input type="checkbox" value="location"<?php echo (strpos($field_list,',location,') !== false ? " checked" : ""); ?>
										name="field_job[]" style="margin:0.5em;">Site Location</label>

										<label><input type="checkbox" value="overview"<?php echo (strpos($field_list,',overview,') !== false ? " checked" : ""); ?>
										name="field_job[]" style="margin:0.5em;">Job Overview</label>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<?php if($current_tab == 'foreman'):
						if(str_replace(',','',$dashboard_list) == '') {
							$dashboard_list = ',job,crew,';
						}
						if(str_replace(',','',$field_list) == '') {
							$field_list = ',job,date,afe,additional,site,description,crew_name,crew_pos,crew_reg,crew_ot,crew_travel,crew_sub,equipment,stock_desc,stock_qty,stock_price,stock_amount,comments,';
						} ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_foreman_dashboard">
										Foreman Sheet Dashboard<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_foreman_dashboard" class="panel-collapse collapse">
								<div class="panel-body">
									<div class="no-more-tables">
										<label><input type="checkbox" value="job"<?php echo (strpos($dashboard_list,',job,') !== false ? " checked" : ""); ?>
										name="dashboard_foreman[]" style="margin:0.5em;">Job #</label>

										<label><input type="checkbox" value="date"<?php echo (strpos($dashboard_list,',date,') !== false ? " checked" : ""); ?>
										name="dashboard_foreman[]" style="margin:0.5em;">Date</label>

										<label><input type="checkbox" value="contact"<?php echo (strpos($dashboard_list,',contact,') !== false ? " checked" : ""); ?>
										name="dashboard_foreman[]" style="margin:0.5em;">Contact</label>

										<label><input type="checkbox" value="crew"<?php echo (strpos($dashboard_list,',crew,') !== false ? " checked" : ""); ?>
										name="dashboard_foreman[]" style="margin:0.5em;">Crew Info</label>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_foreman_fields">
										Foreman Sheet Fields<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_foreman_fields" class="panel-collapse collapse">
								<div class="panel-body">
									<div class="no-more-tables">
										<label><input type="checkbox" value="job"<?php echo (strpos($field_list,',job,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Job #</label>

										<label><input type="checkbox" value="date"<?php echo (strpos($field_list,',date,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Sheet Date</label>

										<label><input type="checkbox" value="afe"<?php echo (strpos($field_list,',afe,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">AFE #</label>

										<label><input type="checkbox" value="additional"<?php echo (strpos($field_list,',additional,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Additional Info</label>

										<label><input type="checkbox" value="site"<?php echo (strpos($field_list,',site,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Site Location</label>

										<label><input type="checkbox" value="description"<?php echo (strpos($field_list,',description,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Description</label>

										<label><input type="checkbox" value="crew_name"<?php echo (strpos($field_list,',crew_name,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Crew Name</label>

										<label><input type="checkbox" value="crew_pos"<?php echo (strpos($field_list,',crew_pos,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Crew Position</label>

										<label><input type="checkbox" value="crew_rate"<?php echo (strpos($field_list,',crew_rate,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Crew Hourly Rate</label>

										<label><input type="checkbox" value="crew_reg"<?php echo (strpos($field_list,',crew_reg,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Crew Billable Regular</label>

										<label><input type="checkbox" value="crew_ot"<?php echo (strpos($field_list,',crew_ot,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Crew Overtime</label>

										<label><input type="checkbox" value="crew_travel"<?php echo (strpos($field_list,',crew_travel,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Crew Travel</label>

										<label><input type="checkbox" value="crew_sub"<?php echo (strpos($field_list,',crew_sub,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Crew Sub</label>

										<label><input type="checkbox" value="equipment"<?php echo (strpos($field_list,',equipment,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Equipment</label>

										<label><input type="checkbox" value="stock_desc"<?php echo (strpos($field_list,',stock_desc,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Stock / Material Description</label>

										<label><input type="checkbox" value="stock_qty"<?php echo (strpos($field_list,',stock_qty,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Stock / Material Quantity</label>

										<label><input type="checkbox" value="stock_price"<?php echo (strpos($field_list,',stock_price,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Stock / Material Unit Price</label>

										<label><input type="checkbox" value="stock_amount"<?php echo (strpos($field_list,',stock_amount,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Stock / Material Amount</label>

										<label><input type="checkbox" value="comments"<?php echo (strpos($field_list,',comments,') !== false ? " checked" : ""); ?>
										name="field_foreman[]" style="margin:0.5em;">Comments</label>
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_foreman_fields1">
										Foreman Sheet Approval Emails<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_foreman_fields1" class="panel-collapse collapse">
								<div class="panel-body">

                                  <div class="form-group">
                                    <label for="office_state" class="col-sm-4 control-label">From Email:</em></label>
                                    <div class="col-sm-8">
                                      <input name="fs_from_email" type="text" value="<?php echo get_config($dbc, 'fs_from_email'); ?>" class="form-control"/>
                                    </div>
                                  </div>

                                  <div class="form-group">
                                    <label for="office_state" class="col-sm-4 control-label">Approval Email(s):<br><em>(If sending multiple email, separate with a comma using no spaces)</em></label>
                                    <div class="col-sm-8">
                                      <input name="fs_approval_email" type="text" value="<?php echo get_config($dbc, 'fs_approval_email'); ?>" class="form-control"/>
                                    </div>
                                  </div>

                                  <div class="form-group">
                                    <label for="office_state" class="col-sm-4 control-label">Supervisor Process:<br><em>(If sending multiple email, separate with a comma using no spaces)</em></label>
                                    <div class="col-sm-8">
                                      <input name="fs_supervisor_email" type="text" value="<?php echo get_config($dbc, 'fs_supervisor_email'); ?>" class="form-control"/>
                                    </div>
                                  </div>

								</div>
							</div>
						</div>
					<?php endif; ?>
					<?php if($current_tab == 'po'):
						if(str_replace(',','',$dashboard_list) == '') {
							$dashboard_list = ',po,job,';
						}
						if(str_replace(',','',$field_list) == '') {
							$field_list = ',job,po,billable,type,vendor,date,item_qty,item_desc,item_grade,item_tag,item_detail,item_price,item_cost,description,cost,tax,total,';
						} ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_po_dashboard">
										PO Dashboard<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_po_dashboard" class="panel-collapse collapse">
								<div class="panel-body">
									<div class="no-more-tables">
										<label><input type="checkbox" value="po"<?php echo (strpos($dashboard_list,',po,') !== false ? " checked" : ""); ?>
										name="dashboard_po[]" style="margin:0.5em;">PO #</label>

										<label><input type="checkbox" value="job"<?php echo (strpos($dashboard_list,',job,') !== false ? " checked" : ""); ?>
										name="dashboard_po[]" style="margin:0.5em;">Job #</label>

										<label><input type="checkbox" value="vendor"<?php echo (strpos($dashboard_list,',vendor,') !== false ? " checked" : ""); ?>
										name="dashboard_po[]" style="margin:0.5em;">Vendor</label>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_po_fields">
										PO Fields<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_po_fields" class="panel-collapse collapse">
								<div class="panel-body">
									<div class="no-more-tables">
										<label><input type="checkbox" value="job"<?php echo (strpos($field_list,',job,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Job #</label>

										<label><input type="checkbox" value="po"<?php echo (strpos($field_list,',po,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">PO #</label>

										<label><input type="checkbox" value="billable"<?php echo (strpos($field_list,',billable,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Billable</label>

										<label><input type="checkbox" value="type"<?php echo (strpos($field_list,',type,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Type</label>

										<label><input type="checkbox" value="vendor"<?php echo (strpos($field_list,',vendor,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Vendor</label>

										<label><input type="checkbox" value="3rd party"<?php echo (strpos($field_list,',3rd party,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">3rd Party Invoice #</label>

										<label><input type="checkbox" value="invoice"<?php echo (strpos($field_list,',invoice,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Invoice</label>

										<label><input type="checkbox" value="date"<?php echo (strpos($field_list,',date,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Date</label>

										<label><input type="checkbox" value="item_qty"<?php echo (strpos($field_list,',item_qty,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Item Quantity</label>

										<label><input type="checkbox" value="item_desc"<?php echo (strpos($field_list,',item_desc,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Item Description</label>

										<label><input type="checkbox" value="item_grade"<?php echo (strpos($field_list,',item_grade,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Item Grade</label>

										<label><input type="checkbox" value="item_tag"<?php echo (strpos($field_list,',item_tag,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Item Tag</label>

										<label><input type="checkbox" value="item_detail"<?php echo (strpos($field_list,',item_detail,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Item Detail</label>

										<label><input type="checkbox" value="item_price"<?php echo (strpos($field_list,',item_price,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Item Unit Price</label>

										<label><input type="checkbox" value="item_cost"<?php echo (strpos($field_list,',item_cost,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Item Cost</label>

										<label><input type="checkbox" value="description"<?php echo (strpos($field_list,',description,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Description</label>

										<label><input type="checkbox" value="cost"<?php echo (strpos($field_list,',cost,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Cost</label>

										<label><input type="checkbox" value="tax"<?php echo (strpos($field_list,',tax,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Sales Tax</label>

										<label><input type="checkbox" value="total"<?php echo (strpos($field_list,',total,') !== false ? " checked" : ""); ?>
										name="field_po[]" style="margin:0.5em;">Total Cost</label>
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_po_fields1">
										PO PDF<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_po_fields1" class="panel-collapse collapse">
								<div class="panel-body">

                                    <div class="form-group">
                                        <label for="file[]" class="col-sm-4 control-label">Header Logo
                                        <span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
                                        </span>
                                        :</label>
                                        <div class="col-sm-8">
                                            <?php
                                                $field_jobs_po_logo = get_config($dbc, 'field_jobs_po_logo');
                                                if($field_jobs_po_logo != '') {
                                                echo '<a href="download/'.$field_jobs_po_logo.'" target="_blank">View</a>';
                                                ?>
                                                <input type="hidden" name="logo_file" value="<?php echo $field_jobs_po_logo; ?>" />
                                                <input name="field_jobs_po_logo" type="file" data-filename-placement="inside" class="form-control" />
                                            <?php } else { ?>
                                                <input name="field_jobs_po_logo" type="file" data-filename-placement="inside" class="form-control" />
                                            <?php } ?>
                                        </div>
                                    </div>

                                   <?php
                                    $field_jobs_po_address = html_entity_decode(get_config($dbc, 'field_jobs_po_address'));
                                   ?>

                                  <div class="form-group">
                                    <label for="fax_number"	class="col-sm-4	control-label">Header Address & Email:</label>
                                    <div class="col-sm-8">
                                        <textarea name="field_jobs_po_address" rows="5" cols="50" class="form-control"><?php echo $field_jobs_po_address; ?></textarea>
                                    </div>
                                  </div>

								</div>
							</div>
						</div>
					<?php endif; ?>
					<?php if($current_tab == 'work'):
						if(str_replace(',','',$dashboard_list) == '') {
							$dashboard_list = ',ticket,job,date,description,mod_reg,mod_ot,';
						}
						if(str_replace(',','',$field_list) == '') {
							$field_list = ',work_ticket,date,job,customer,invoice,sent,approved,';
						} ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_work_dashboard">
										Work Ticket Dashboard<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_work_dashboard" class="panel-collapse collapse">
								<div class="panel-body">
									<div class="no-more-tables">
										<label><input type="checkbox" value="work_ticket"<?php echo (strpos($dashboard_list,',work_ticket,') !== false ? " checked" : ""); ?>
										name="dashboard_work[]" style="margin:0.5em;">Work Ticket #</label>

										<label><input type="checkbox" value="date"<?php echo (strpos($dashboard_list,',date,') !== false ? " checked" : ""); ?>
										name="dashboard_work[]" style="margin:0.5em;">Date</label>

										<label><input type="checkbox" value="job"<?php echo (strpos($dashboard_list,',job,') !== false ? " checked" : ""); ?>
										name="dashboard_work[]" style="margin:0.5em;">Job #</label>

										<label><input type="checkbox" value="customer"<?php echo (strpos($dashboard_list,',customer,') !== false ? " checked" : ""); ?>
										name="dashboard_work[]" style="margin:0.5em;">Customer</label>

										<label><input type="checkbox" value="invoice"<?php echo (strpos($dashboard_list,',invoice,') !== false ? " checked" : ""); ?>
										name="dashboard_work[]" style="margin:0.5em;">Invoice</label>

										<label><input type="checkbox" value="sent"<?php echo (strpos($dashboard_list,',sent,') !== false ? " checked" : ""); ?>
										name="dashboard_work[]" style="margin:0.5em;">Date Sent</label>

										<label><input type="checkbox" value="approved"<?php echo (strpos($dashboard_list,',approved,') !== false ? " checked" : ""); ?>
										name="dashboard_work[]" style="margin:0.5em;">Date Approved</label>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_work_fields">
										Work Ticket Fields<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_work_fields" class="panel-collapse collapse">
								<div class="panel-body">
									<div class="no-more-tables">
										<label><input type="checkbox" value="ticket"<?php echo (strpos($field_list,',ticket,') !== false ? " checked" : ""); ?>
										name="field_work[]" style="margin:0.5em;">Work Ticket #</label>

										<label><input type="checkbox" value="job"<?php echo (strpos($field_list,',job,') !== false ? " checked" : ""); ?>
										name="field_work[]" style="margin:0.5em;">Job #</label>

										<label><input type="checkbox" value="date"<?php echo (strpos($field_list,',date,') !== false ? " checked" : ""); ?>
										name="field_work[]" style="margin:0.5em;">Date</label>

										<label><input type="checkbox" value="description"<?php echo (strpos($field_list,',description,') !== false ? " checked" : ""); ?>
										name="field_work[]" style="margin:0.5em;">Description</label>

										<label><input type="checkbox" value="mod_reg"<?php echo (strpos($field_list,',mod_reg,') !== false ? " checked" : ""); ?>
										name="field_work[]" style="margin:0.5em;">Modified Regular Pay</label>

										<label><input type="checkbox" value="mod_ot"<?php echo (strpos($field_list,',mod_ot,') !== false ? " checked" : ""); ?>
										name="field_work[]" style="margin:0.5em;">Modified Overtime</label>
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_po_fields12">
										Work Ticket PDF<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_po_fields12" class="panel-collapse collapse">
								<div class="panel-body">

                                    <div class="form-group">
                                        <label for="file[]" class="col-sm-4 control-label">Header Logo
                                        <span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
                                        </span>
                                        :</label>
                                        <div class="col-sm-8">
                                            <?php
                                                $field_jobs_wt_logo = get_config($dbc, 'field_jobs_wt_logo');
                                                if($field_jobs_wt_logo != '') {
                                                echo '<a href="download/'.$field_jobs_wt_logo.'" target="_blank">View</a>';
                                                ?>
                                                <input type="hidden" name="logo_file_wt" value="<?php echo $field_jobs_wt_logo; ?>" />
                                                <input name="field_jobs_wt_logo" type="file" data-filename-placement="inside" class="form-control" />
                                            <?php } else { ?>
                                                <input name="field_jobs_wt_logo" type="file" data-filename-placement="inside" class="form-control" />
                                            <?php } ?>
                                        </div>
                                    </div>

                                   <?php
                                    $field_jobs_wt_address = html_entity_decode(get_config($dbc, 'field_jobs_wt_address'));
                                   ?>

                                  <div class="form-group">
                                    <label for="fax_number"	class="col-sm-4	control-label">Header Address & Email:</label>
                                    <div class="col-sm-8">
                                        <textarea name="field_jobs_wt_address" rows="5" cols="50" class="form-control"><?php echo $field_jobs_wt_address; ?></textarea>
                                    </div>
                                  </div>

								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_foreman_fields1">
										Work Ticket Email<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_foreman_fields1" class="panel-collapse collapse">
								<div class="panel-body">

                                  <div class="form-group">
                                    <label for="office_state" class="col-sm-4 control-label">Default From:<br /><em>The default reply-to email address. If left blank, it will populate with the current user's email address.</em></label>
                                    <div class="col-sm-8">
                                      <input name="wt_email_from" type="text" value="<?php echo get_config($dbc, 'wt_email_from'); ?>" class="form-control"/>
                                    </div>
                                  </div>

                                  <div class="form-group">
                                    <label for="office_state" class="col-sm-4 control-label">BCC Email:</label>
                                    <div class="col-sm-8">
                                      <input name="wt_bcc_email" type="text" value="<?php echo get_config($dbc, 'wt_bcc_email'); ?>" class="form-control"/>
                                    </div>
                                  </div>

                                  <div class="form-group">
                                    <label for="office_state" class="col-sm-4 control-label">Email Subject:</label>
                                    <div class="col-sm-8">
										<?php $subject = get_config($dbc, 'wt_email_subject');
										if($subject == '') {
											$subject = 'Please review the attached Work Ticket';
										} ?>
										<input name="wt_email_subject" type="text" value="<?php echo $subject; ?>" class="form-control"/>
                                    </div>
                                  </div>

                                  <div class="form-group">
                                    <label for="office_state" class="col-sm-4 control-label">Email Body:</label>
                                    <div class="col-sm-8">
										<?php $body = html_entity_decode(get_config($dbc, 'wt_email_body'));
										if($body == '') {
											$body = 'Attached to this email is a Work Ticket. Please review it, and let us know if you have any concerns.';
										} ?>
										<textarea name="wt_email_body" class="form-control"><?php echo $body; ?></textarea>
                                    </div>
                                  </div>

								</div>
							</div>
						</div>

					<?php endif; ?>
					<?php if($current_tab == 'invoice'):
						if(str_replace(',','',$dashboard_list) == '') {
							$dashboard_list = ',invoice,job,customer,';
						}
						if(str_replace(',','',$field_list) == '') {
							$field_list = ',,';
						} ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_invoice_dashboard">
										Invoice Dashboard<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_invoice_dashboard" class="panel-collapse collapse">
								<div class="panel-body">
									<div class="no-more-tables">
										<label><input type="checkbox" value="invoice"<?php echo (strpos($dashboard_list,',invoice,') !== false ? " checked" : ""); ?>
										name="dashboard_invoice[]" style="margin:0.5em;">Invoice #</label>

										<label><input type="checkbox" value="job"<?php echo (strpos($dashboard_list,',job,') !== false ? " checked" : ""); ?>
										name="dashboard_invoice[]" style="margin:0.5em;">Job #</label>

										<label><input type="checkbox" value="customer"<?php echo (strpos($dashboard_list,',customer,') !== false ? " checked" : ""); ?>
										name="dashboard_invoice[]" style="margin:0.5em;">Customer</label>

										<label><input type="checkbox" value="date"<?php echo (strpos($dashboard_list,',date,') !== false ? " checked" : ""); ?>
										name="dashboard_invoice[]" style="margin:0.5em;">Created Date</label>
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_po_fields123">
										Invoice PDF<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_po_fields123" class="panel-collapse collapse">
								<div class="panel-body">

                                    <div class="form-group">
                                        <label for="file[]" class="col-sm-4 control-label">Header Logo
                                        <span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
                                        </span>
                                        :</label>
                                        <div class="col-sm-8">
                                            <?php
                                                $field_jobs_invoice_logo = get_config($dbc, 'field_jobs_invoice_logo');
                                                if($field_jobs_invoice_logo != '') {
                                                echo '<a href="download/'.$field_jobs_invoice_logo.'" target="_blank">View</a>';
                                                ?>
                                                <input type="hidden" name="logo_file_invoice" value="<?php echo $field_jobs_invoice_logo; ?>" />
                                                <input name="field_jobs_invoice_logo" type="file" data-filename-placement="inside" class="form-control" />
                                            <?php } else { ?>
                                                <input name="field_jobs_invoice_logo" type="file" data-filename-placement="inside" class="form-control" />
                                            <?php } ?>
                                        </div>
                                    </div>

                                   <?php
                                    $field_jobs_invoice_address = html_entity_decode(get_config($dbc, 'field_jobs_invoice_address'));
                                   ?>

                                  <div class="form-group">
                                    <label for="fax_number"	class="col-sm-4	control-label">Header Address & Email:</label>
                                    <div class="col-sm-8">
                                        <textarea name="field_jobs_invoice_address" rows="5" cols="50" class="form-control"><?php echo $field_jobs_invoice_address; ?></textarea>
                                    </div>
                                  </div>

								</div>
							</div>
						</div>

					<?php endif; ?>
					<?php if($current_tab == 'payroll'):
						if(str_replace(',','',$dashboard_list) == '') {
							$dashboard_list = ',contact,ratio,reg,ot,';
						}
						if(str_replace(',','',$field_list) == '') {
							$field_list = ',,';
						} ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_config" href="#collapse_payroll_dashboard">
										Payroll Dashboard<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>
							<div id="collapse_payroll_dashboard" class="panel-collapse collapse">
								<div class="panel-body">
									<div class="no-more-tables">
										<label><input type="checkbox" value="contact"<?php echo (strpos($dashboard_list,',contact,') !== false ? " checked" : ""); ?>
										name="dashboard_payroll[]" style="margin:0.5em;">Name</label>

										<label><input type="checkbox" value="ratio"<?php echo (strpos($dashboard_list,',ratio,') !== false ? " checked" : ""); ?>
										name="dashboard_payroll[]" style="margin:0.5em;">Reg - OT Ratio</label>

										<label><input type="checkbox" value="reg"<?php echo (strpos($dashboard_list,',reg,') !== false ? " checked" : ""); ?>
										name="dashboard_payroll[]" style="margin:0.5em;">Total Regular</label>

										<label><input type="checkbox" value="ot"<?php echo (strpos($dashboard_list,',ot,') !== false ? " checked" : ""); ?>
										name="dashboard_payroll[]" style="margin:0.5em;">Total OT</label>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-6"><a href="<?php echo $url; ?>" class="btn config-btn btn-lg">Back</a></div>
				<div class="col-sm-6">
					<?php if($current_tab == 'manage'){ ?>
					<button type="submit" value="Submit" name="submit_tabs" class="btn config-btn btn-lg pull-right">Submit</button>
					<?php } elseif($current_tab == 'sites')  { ?>
					<button type="submit" value="Submit" name="submit_sites" class="btn config-btn btn-lg pull-right">Submit</button>
					<?php } elseif($current_tab == 'jobs')  { ?>
					<button type="submit" value="Submit" name="submit_jobs" class="btn config-btn btn-lg pull-right">Submit</button>
					<?php } elseif($current_tab == 'foreman')  { ?>
					<button type="submit" value="Submit" name="submit_foreman" class="btn config-btn btn-lg pull-right">Submit</button>
					<?php } elseif($current_tab == 'po')  { ?>
					<button type="submit" value="Submit" name="submit_po" class="btn config-btn btn-lg pull-right">Submit</button>
					<?php } elseif($current_tab == 'work')  { ?>
					<button type="submit" value="Submit" name="submit_work" class="btn config-btn btn-lg pull-right">Submit</button>
					<?php } elseif($current_tab == 'invoice')  { ?>
					<button type="submit" value="Submit" name="submit_invoice" class="btn config-btn btn-lg pull-right">Submit</button>
					<?php } else { ?>
					<button type="submit" value="Submit" name="submit_payroll" class="btn config-btn btn-lg pull-right">Submit</button>
					<?php  } ?>
				</div>
				<div class="clearfix"></div>
			</div>
		</form>
	</div>
</div>

<?php include ('../footer.php'); ?>
