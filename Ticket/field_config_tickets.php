<?php
/*
Dashboard
*/
include_once('../include.php');
error_reporting(0);

$from_url = 'tickets.php';
if(!empty($_GET['from_url'])) {
	$from_url = urldecode($_GET['from_url']);
}
if (isset($_POST['submit'])) {
    //Ticket Fields
    $tickets  = implode(',',$_POST['tickets']);
    $tickets_dashboard = implode(',',$_POST['tickets_dashboard']);
	$colours = filter_var(implode(',',$_POST['flag_colours']),FILTER_SANITIZE_STRING);
	$flag_names = filter_var(implode('#*#',$_POST['flag_name']),FILTER_SANITIZE_STRING);

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config"));
    if($get_field_config['fieldconfigid'] > 0) {
        $query_update_employee = "UPDATE `field_config` SET tickets = '$tickets', tickets_dashboard = '$tickets_dashboard' WHERE `fieldconfigid` = 1";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config` (`tickets`, `tickets_dashboard`) VALUES ('$tickets', '$tickets_dashboard')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    $auto_archive = filter_var($_POST['auto_archive'],FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(*) AS rows FROM general_configuration WHERE name='auto_archive_complete_tickets'"))['rows'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$auto_archive' WHERE name='auto_archive_complete_tickets'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('auto_archive_complete_tickets', '$auto_archive')");
    }
    $ticket_label = filter_var($_POST['ticket_label'],FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(*) AS rows FROM general_configuration WHERE name='ticket_label'"))['rows'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$ticket_label' WHERE name='ticket_label'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_label', '$ticket_label')");
    }
    $ticket_sorting = filter_var($_POST['ticket_sorting'],FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(*) AS rows FROM general_configuration WHERE name='ticket_sorting'"))['rows'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$ticket_sorting' WHERE name='ticket_sorting'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_sorting', '$ticket_sorting')");
    }

		$default_status = filter_var($_POST['default_status'],FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(*) AS rows FROM general_configuration WHERE name='ticket_default_status'"))['rows'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$default_status' WHERE name='ticket_default_status'");
				mysqli_query($dbc, "ALTER TABLE tickets ALTER COLUMN status SET DEFAULT '$default_status'");
    } else {
				mysqli_query($dbc, "ALTER TABLE tickets ALTER COLUMN status SET DEFAULT '$default_status'");
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_default_status', '$default_status')");
    }
    //Ticket Fields

    //Multiple Ticket Labels
    $ticket_multiple_labels  = filter_var($_POST['ticket_multiple_labels'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_multiple_labels'"));
    if($get_config['configid'] > 0) {
        $result = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$ticket_multiple_labels' WHERE name='ticket_multiple_labels'");
    } else {
        $result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_multiple_labels', '$ticket_multiple_labels')");
    }
    //Multiple Ticket Labels

	//Staff Tasks
	$tasks = filter_var($_POST['ticket_ALL_staff_tasks'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_ALL_staff_tasks'"));
    if($get_config['configid'] > 0) {
        $result = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$tasks' WHERE name='ticket_ALL_staff_tasks'");
    } else {
        $result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_ALL_staff_tasks', '$tasks')");
    }
	//Staff Tasks

	//Extra Billing Notice
	$ticket_extra_billing_email = filter_var($_POST['ticket_extra_billing_email'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_extra_billing_email'"));
    if($get_config['configid'] > 0) {
        $result = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$ticket_extra_billing_email' WHERE name='ticket_extra_billing_email'");
    } else {
        $result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_extra_billing_email', '$ticket_extra_billing_email')");
    }
	//Extra Billing Notice

    //Tile Labels
	if($_POST['ticket_noun'] == '') {
		$_POST['ticket_noun'] = $_POST['ticket_tile'];
	}
    $ticket_tile_name = filter_var($_POST['ticket_tile'].'#*#'.$_POST['ticket_noun'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_tile_name'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$ticket_tile_name' WHERE name='ticket_tile_name'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_tile_name', '$ticket_tile_name')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    $ticket_tabs = filter_var($_POST['ticket_tabs'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_tabs'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$ticket_tabs' WHERE name='ticket_tabs'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_tabs', '$ticket_tabs')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    $ticket_type_tiles = filter_var($_POST['ticket_type_tiles'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_type_tiles'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$ticket_type_tiles' WHERE name='ticket_type_tiles'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_type_tiles', '$ticket_type_tiles')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Tile Labels

    //Task Status
    $task_status = filter_var($_POST['task_status'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='task_status'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$task_status' WHERE name='task_status'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('task_status', '$task_status')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Task Status

    //Staff Groups
    $ticket_groups = [ [] ];
	$i = 0;
	foreach($_POST['ticket_groups'] as $id) {
		if($id > 0) {
			$ticket_groups[$i][] = $id;
		} else {
			$i++;
			$ticket_groups[$i][] = $id;
		}
	}
	foreach($ticket_groups as $i => $group) {
		$ticket_groups[$i] = implode(',',$group);
	}
	$ticket_groups = filter_var(implode('#*#',array_filter($ticket_groups)),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_groups'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$ticket_groups' WHERE name='ticket_groups'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_groups', '$ticket_groups')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Staff Groups

    //Ticket Status
    $ticket_status = filter_var($_POST['ticket_status'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_status'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$ticket_status' WHERE name='ticket_status'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_status', '$ticket_status')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Ticket Status

	//Ticket Security
	$ticket_roles = [];
	foreach($_POST['role'] as $i => $role) {
		$ticket_roles[] = filter_var(implode('|',[$role,($_POST['default_security'] == $role ? 'default' : ''),$_POST['role_project'][$i],$_POST['role_staff'][$i],$_POST['role_contacts'][$i],$_POST['role_wait'][$i],$_POST['role_staff_checkin'][$i],$_POST['role_checkin'][$i],$_POST['role_meds'][$i],$_POST['role_complete'][$i],$_POST['role_ticket'][$i]]),FILTER_SANITIZE_STRING);
	}
	$ticket_roles = implode('#*#',$ticket_roles);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(*) AS rows FROM general_configuration WHERE name='ticket_roles'"))['rows'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$ticket_roles' WHERE name='ticket_roles'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_roles', '$ticket_roles')");
    }

    //Ticket Colours
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_colour_flags'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$colours' WHERE name='ticket_colour_flags'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_colour_flags', '$colours')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Ticket Colours

    //Ticket Colour Labels
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_colour_flag_names'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$flag_names' WHERE name='ticket_colour_flag_names'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_colour_flag_names', '$flag_names')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Ticket Colour Labels

    //Ticket PDF Settings
    $ticket_pdf_header_left  = filter_var(htmlentities($_POST['ticket_pdf_header_left']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_pdf_header_left'"));
    if($get_config['configid'] > 0) {
        $result = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$ticket_pdf_header_left' WHERE name='ticket_pdf_header_left'");
    } else {
        $result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_pdf_header_left', '$ticket_pdf_header_left')");
    }
    $ticket_pdf_header_right  = filter_var(htmlentities($_POST['ticket_pdf_header_right']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_pdf_header_right'"));
    if($get_config['configid'] > 0) {
        $result = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$ticket_pdf_header_right' WHERE name='ticket_pdf_header_right'");
    } else {
        $result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_pdf_header_right', '$ticket_pdf_header_right')");
    }
    $ticket_pdf_footer  = filter_var(htmlentities($_POST['ticket_pdf_footer']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_pdf_footer'"));
    if($get_config['configid'] > 0) {
        $result = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$ticket_pdf_footer' WHERE name='ticket_pdf_footer'");
    } else {
        $result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_pdf_footer', '$ticket_pdf_footer')");
    }
	$ticket_pdf_logo = $_FILES['ticket_pdf_logo'];
	if($ticket_pdf_logo['name'] != '') {
		$basename = preg_replace('/[^\.A-Za-z0-9]/','',$ticket_pdf_logo['name']);
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', $basename);
		for($i = 1; file_exists('download/'.$filename); $i++) {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.$i.')$1', $basename);
		}
		move_uploaded_file($ticket_pdf_logo['tmp_name'],'download/'.$filename);
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_pdf_logo'"));
		if($get_config['configid'] > 0) {
			$result = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$filename' WHERE name='ticket_pdf_logo'");
		} else {
			$result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_pdf_logo', '$filename')");
		}
	}
    //Ticket PDF Settings

    echo '<script type="text/javascript"> window.location.replace("'.$from_url.'"); </script>';

}
?>
</head>
<body>

<?php include_once('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1><?= TICKET_TILE ?></h1>
<div class="pad-left gap-top double-gap-bottom">
	<a href="<?php echo $from_url; ?>" class="btn brand-btn">Back to Dashboard</a>
	<?php foreach(array_filter(explode(',',get_config($dbc, 'ticket_tabs'))) as $ticket_tab) { ?>
		<a href="field_config_ticket_fields.php?tab=<?= config_safe_str($ticket_tab) ?>&from_url=<?= urlencode($from_url) ?>" class="btn brand-btn"><?= $ticket_tab ?> Fields</a>
	<?php } ?>
</div>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<?php
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `tickets`, `tickets_dashboard` FROM `field_config`"));
$value_config = explode(',',$get_field_config['tickets']);
$all_config = [];
$db_config = ','.$get_field_config['tickets_dashboard'].',';
$flag_colours = get_config($dbc, 'ticket_colour_flags');
$flag_names = explode('#*#', get_config($dbc, 'ticket_colour_flag_names'));
?>

    <div class="panel-group" id="accordion2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add or remove the fields."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_db" >
                        Choose Fields for Dashboard<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_db" class="panel-collapse collapse">
                <div class="panel-body">
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Project".',') !== false) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> <?= PROJECT_NOUN ?> Information</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Business".',') !== false) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Business</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Contact".',') !== false) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Contact</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Services".',') !== false) { echo " checked"; } ?> value="Services" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Services</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Heading".',') !== false) { echo " checked"; } ?> value="Heading" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Heading</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Staff".',') !== false) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Staff</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Members".',') !== false) { echo " checked"; } ?> value="Members" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Members</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Clients".',') !== false) { echo " checked"; } ?> value="Clients" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Clients</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Deliverable Date".',') !== false) { echo " checked"; } ?> value="Deliverable Date" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Deliverable Dates</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Ticket Date".',') !== false) { echo " checked"; } ?> value="Ticket Date" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> <?= TICKET_NOUN ?> Dates</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Status".',') !== false) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Status</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."PDF".',') !== false) { echo " checked"; } ?> value="PDF" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> PDF Download</label>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Export".',') !== false) { echo " checked"; } ?> value="Export" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Import / Export Button</label>
					<div class="form-group">
						<div class="col-sm-4">
							<!--<a href="" class="btn brand-btn">Back</a>-->
							<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>
						</div>
						<div class="col-sm-8">
							<span class="popover-examples list-inline pull-right" style="margin-left:-6em;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="The entire form will submit and close if this submit button is pressed.">
								<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed." onclick="return confirm('The entire form will submit and close if this submit button is pressed.')" class="btn brand-btn">Submit</button></a></span>
							<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed." onclick="return confirm('The entire form will submit and close if this submit button is pressed.')" class="btn brand-btn pull-right">Submit</button>
						</div>
					</div>
                </div>

            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add or remove the fields."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
                        Choose Fields for All <?= TICKET_TILE ?><span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_field" class="panel-collapse collapse">
                <div class="panel-body">
					<?php include('field_config_field_list.php'); ?>
					<div class="form-group">
						<div class="col-sm-4">
							<!--<a href="" class="btn brand-btn">Back</a>-->
							<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>
						</div>
						<div class="col-sm-8">
							<span class="popover-examples list-inline pull-right" style="margin-left:-6em;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="The entire form will submit and close if this submit button is pressed.">
								<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed." onclick="return confirm('The entire form will submit and close if this submit button is pressed.')" class="btn brand-btn">Submit</button></a></span>
							<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed." onclick="return confirm('The entire form will submit and close if this submit button is pressed.')" class="btn brand-btn pull-right">Submit</button>
						</div>
					</div>
                </div>

            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add or remove the fields."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pdf" >
                        Choose Settings for PDFs<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_pdf" class="panel-collapse collapse">
                <div class="panel-body">
					<div class="form-group">
						<label class="col-sm-4 control-label">Left Header</label>
						<div class="col-sm-12">
							<textarea name="ticket_pdf_header_left"><?= get_config($dbc, 'ticket_pdf_header_left') ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Right Header</label>
						<div class="col-sm-8">
							<textarea name="ticket_pdf_header_right"><?= get_config($dbc, 'ticket_pdf_header_right') ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Footer</label>
						<div class="col-sm-8">
							<textarea name="ticket_pdf_footer"><?= get_config($dbc, 'ticket_pdf_footer') ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Logo</label>
						<div class="col-sm-8">
							<?php $pdf_logo = get_config($dbc, 'ticket_pdf_logo');
							if($pdf_logo != '' && file_exists('download/'.$pdf_logo)) {
								echo '<a href="download/'.$pdf_logo.'">View</a>';
							} ?>
							<input type="file" class="form-control" name="ticket_pdf_logo">
						</div>
					</div>
				</div>

            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add or remove the status/heading you want attached to every <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field2" >
                        <?= TICKET_TILE ?> Tile Settings<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_field2" class="panel-collapse collapse">
                <div class="panel-body">

                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label">Tile Name / Ticket Noun:</label>
                        <div class="col-sm-4">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Enter the name you would like the Tickets tile to be labelled as."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                          <input name="ticket_tile" type="text" value="<?= TICKET_TILE ?>" class="form-control"/>
                        </div>
							<div class="col-sm-4"><span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Enter the name you would like individual Tickets to be labelled as."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                          <input name="ticket_noun" type="text" value="<?= TICKET_NOUN ?>" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label">Ticket Label:<br /><em>Enter how you want a <?= TICKET_NOUN ?> to appear. You can enter [PROJECT_NOUN], [PROJECTID], [PROJECT_NAME], [TICKET_NOUN], [TICKETID], [TICKET_HEADING], [TICKET_DATE], [BUSINESS], [CONTACT].</em></label>
                        <div class="col-sm-8">
							<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter the format you would like Tickets to be labelled with. You can use [PROJECT_NOUN], [PROJECTID], [PROJECT_NAME], [TICKET_NOUN], [TICKETID], [TICKET_HEADING], [TICKET_DATE]"><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							<input name="ticket_label" type="text" value="<?= get_config($dbc, "ticket_label") ?>" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label"><?= TICKET_NOUN ?> Sorting:</label>
                        <div class="col-sm-8">
							<?php $ticket_sorting = get_config($dbc, "ticket_sorting"); ?>
							<label class="form-checkbox"><input type="radio" <?= ('newest' == $ticket_sorting || $ticket_sorting == '') ? 'checked' : '' ?> name="ticket_sorting" value="newest"> Newest First</label>
							<label class="form-checkbox"><input type="radio" <?= ('oldest' == $ticket_sorting) ? 'checked' : '' ?> name="ticket_sorting" value="oldest"> Oldest First</label>
							<label class="form-checkbox"><input type="radio" <?= ('project' == $ticket_sorting) ? 'checked' : '' ?> name="ticket_sorting" value="project"> <?= PROJECT_NOUN ?> Name (A - Z)</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label"><?= TICKET_TILE ?> Tabs separated by a comma:</label>
                        <div class="col-sm-8">
							<input name="ticket_tabs" type="text" value="<?php echo get_config($dbc, 'ticket_tabs'); ?>" class="form-control"/>
							<label><input name="ticket_type_tiles" type="checkbox" value="SHOW" <?= (get_config($dbc, 'ticket_type_tiles') == 'SHOW' ? 'checked' : '') ?>> Include <?= TICKET_TILE ?> Tabs on Menus</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label"><?= TICKET_NOUN ?> Headings separated by a comma:</label>
                        <div class="col-sm-8">
                          <input name="ticket_status" type="text" value="<?php echo get_config($dbc, 'ticket_status'); ?>" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label">Task Headings separated by a comma:</label>
                        <div class="col-sm-8">
                          <input name="task_status" type="text" value="<?php echo get_config($dbc, 'task_status'); ?>" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label">Auto-Archive Completed Tickets:</label>
                        <div class="col-sm-8">
                          <label><input name="auto_archive" type="radio" value="auto_archive" <?= get_config($dbc, 'auto_archive') == 'auto_archive' ? 'checked' : '' ?> class="form-control"/> Yes</label>
                          <label><input name="auto_archive" type="radio" value="" <?= get_config($dbc, 'auto_archive') == 'auto_archive' ? '' : 'checked' ?> class="form-control"/> No</label>
                        </div>
                    </div>

					<div class="form-group">
						<div class="col-sm-4">
							<!--<a href="" class="btn brand-btn">Back</a>-->
							<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>
						</div>
						<div class="col-sm-8">
							<span class="popover-examples list-inline pull-right" style="margin-left:-6em;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="The entire form will submit and close if this submit button is pressed.">
								<button type="submit" name="submit" value="submit" class="btn brand-btn">Submit</button></a></span>
							<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed." class="btn brand-btn pull-right">Submit</button>
						</div>
					</div>

                </div>
            </div>
        </div>
		<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose and label the colours that will be used for Quick Flags."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_actions" >
                        Quick Flag Colours<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_actions" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="form-group">
						<label for="file[]" class="col-sm-4 control-label">Flag Colours to Use<span class="popover-examples list-inline">&nbsp;
						<a  data-toggle="tooltip" data-placement="top" title="The selected colours will be cycled through when you flag an entry."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
						</span>:</label>
						<div class="col-sm-8">
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'FF6060') !== false ? 'checked' : ''); ?> value="FF6060" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FF6060; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[0]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'DEBAA6') !== false ? 'checked' : ''); ?> value="DEBAA6" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #DEBAA6; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[1]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'FFAEC9') !== false ? 'checked' : ''); ?> value="FFAEC9" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FFAEC9; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[2]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'FFC90E') !== false ? 'checked' : ''); ?> value="FFC90E" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FFC90E; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[3]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'EFE4B0') !== false ? 'checked' : ''); ?> value="EFE4B0" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #EFE4B0; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[4]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'B5E61D') !== false ? 'checked' : ''); ?> value="B5E61D" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #B5E61D; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[5]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, '99D9EA') !== false ? 'checked' : ''); ?> value="99D9EA" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #99D9EA; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[6]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'D0E1F7') !== false ? 'checked' : ''); ?> value="D0E1F7" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #D0E1F7; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[7]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'C8BFE7') !== false ? 'checked' : ''); ?> value="C8BFE7" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #C8BFE7; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[8]; ?>" class="form-control"></div><div class="clearfix"></div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-4">
							<!--<a href="" class="btn brand-btn">Back</a>-->
							<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>
						</div>
						<div class="col-sm-8">
							<span class="popover-examples list-inline pull-right" style="margin-left:-6em;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="The entire form will submit and close if this submit button is pressed.">
								<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed."  class="btn brand-btn">Submit</button></a></span>
							<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed."  class="btn brand-btn pull-right">Submit</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to create roles and security that will apply within a ticket."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_security" >
                        <?= TICKET_NOUN ?> Roles and Security<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_security" class="panel-collapse collapse">
                <div class="panel-body">
					<script>
					$(document).ready(function() {
						toggleSwitch();
					});
					function addRole() {
						var role = $('.role-group').last();
						var clone = role.clone();
						clone.find('input').val('');
						clone.find('.toggleSwitch').each(function() {
							$(this).find('span').last().hide();
							$(this).find('span').first().show();
						});
						role.after(clone);
						toggleSwitch();
					}
					function remRole(img) {
						if($('[name="role[]"]').length == 1) {
							addRole();
						}
						$(img).closest('.role-group').remove();
					}
					function toggleSwitch() {
						$('.toggleSwitch').off('click').click(function() {
							$(this).find('span').toggle();
							var value = $(this).find('.toggle').data('toggle-value');
							$(this).find('.toggle').val($(this).find('.toggle').val() == value ? '' : value).change();
						});
					}
					</script>
                    <div class="form-group">
						<div class="hide-titles-mob">
							<label class="col-sm-2">Role</label>
							<div class="col-sm-10">
								<label class="col-sm-1">Default Role</label>
								<label class="col-sm-1"><?= PROJECT_NOUN ?> Information</label>
								<label class="col-sm-1">Staff Information</label>
								<label class="col-sm-1">Clients / Members</label>
								<label class="col-sm-1">Wait List</label>
								<label class="col-sm-1">Staff Check In / Out</label>
								<label class="col-sm-1">Other Check In / Out</label>
								<label class="col-sm-1">Medication</label>
								<label class="col-sm-1">Complete</label>
								<label class="col-sm-1">All Other</label>
							</div>
						</div>
					</div>
					<?php foreach(explode('#*#',get_config($dbc,'ticket_roles')) as $role_security) {
						$role_security = explode('|',$role_security); ?>
						<div class="form-group role-group">
							<div class="col-sm-2"><label class="show-on-mob">Role:</label>
								<input type="text" name="role[]" class="form-control" value="<?= $role_security[0] ?>" onchange="$(this).closest('.role-group').find('[name=default_security]').val(this.value);">
							</div>
							<div class="col-sm-10">
								<div class="col-sm-1"><label class="show-on-mob">Default Security Level:</label>
									<label class="form-checkbox"><input type="radio" name="default_security" <?= in_array('default',$role_security) ? 'checked' : '' ?> value="<?= $role_security[0] ?>"> Default</label>
								</div>
								<div class="col-sm-1"><label class="show-on-mob"><?= PROJECT_NOUN ?> Information:</label>
									<div class="toggleSwitch">
										<input type="hidden" name="role_project[]" value="<?= in_array('project',$role_security) ? 'project' : '' ?>" class="toggle" data-toggle-value="project">
										<span style="<?= in_array('project',$role_security) ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg no-margin inline-img"></span>
										<span style="<?= in_array('project',$role_security) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg no-margin inline-img"></span>
									</div>
								</div>
								<div class="col-sm-1"><label class="show-on-mob">Staff Information:</label>
									<div class="toggleSwitch">
										<input type="hidden" name="role_staff[]" value="<?= in_array('staff_list',$role_security) ? 'staff_list' : '' ?>" class="toggle" data-toggle-value="staff_list">
										<span style="<?= in_array('staff_list',$role_security) ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg no-margin inline-img"></span>
										<span style="<?= in_array('staff_list',$role_security) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg no-margin inline-img"></span>
									</div>
								</div>
								<div class="col-sm-1"><label class="show-on-mob">Clients / Members Information:</label>
									<div class="toggleSwitch">
										<input type="hidden" name="role_contacts[]" value="<?= in_array('contact_list',$role_security) ? 'contact_list' : '' ?>" class="toggle" data-toggle-value="contact_list">
										<span style="<?= in_array('contact_list',$role_security) ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg no-margin inline-img"></span>
										<span style="<?= in_array('contact_list',$role_security) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg no-margin inline-img"></span>
									</div>
								</div>
								<div class="col-sm-1"><label class="show-on-mob">Wait List:</label>
									<div class="toggleSwitch">
										<input type="hidden" name="role_wait[]" value="<?= in_array('wait_list',$role_security) ? 'wait_list' : '' ?>" class="toggle" data-toggle-value="wait_list">
										<span style="<?= in_array('wait_list',$role_security) ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg no-margin inline-img"></span>
										<span style="<?= in_array('wait_list',$role_security) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg no-margin inline-img"></span>
									</div>
								</div>
								<div class="col-sm-1"><label class="show-on-mob">Staff Check In / Check Out / Staff Summary:</label>
									<div class="toggleSwitch">
										<input type="hidden" name="role_staff_checkin[]" value="<?= in_array('staff_checkin',$role_security) ? 'staff_checkin' : '' ?>" class="toggle" data-toggle-value="staff_checkin">
										<span style="<?= in_array('staff_checkin',$role_security) ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg no-margin inline-img"></span>
										<span style="<?= in_array('staff_checkin',$role_security) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg no-margin inline-img"></span>
									</div>
								</div>
								<div class="col-sm-1"><label class="show-on-mob">Other Check In / Check Out:</label>
									<div class="toggleSwitch">
										<input type="hidden" name="role_checkin[]" value="<?= in_array('all_checkin',$role_security) ? 'all_checkin' : '' ?>" class="toggle" data-toggle-value="all_checkin">
										<span style="<?= in_array('all_checkin',$role_security) ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg no-margin inline-img"></span>
										<span style="<?= in_array('all_checkin',$role_security) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg no-margin inline-img"></span>
									</div>
								</div>
								<div class="col-sm-1"><label class="show-on-mob">Medication Administration:</label>
									<div class="toggleSwitch">
										<input type="hidden" name="role_meds[]" value="<?= in_array('medication',$role_security) ? 'medication' : '' ?>" class="toggle" data-toggle-value="medication">
										<span style="<?= in_array('medication',$role_security) ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg no-margin inline-img"></span>
										<span style="<?= in_array('medication',$role_security) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg no-margin inline-img"></span>
									</div>
								</div>
								<div class="col-sm-1"><label class="show-on-mob">Complete <?= TICKET_NOUN ?>:</label>
									<div class="toggleSwitch">
										<input type="hidden" name="role_complete[]" value="<?= in_array('complete',$role_security) ? 'complete' : '' ?>" class="toggle" data-toggle-value="complete">
										<span style="<?= in_array('complete',$role_security) ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg no-margin inline-img"></span>
										<span style="<?= in_array('complete',$role_security) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg no-margin inline-img"></span>
									</div>
								</div>
								<div class="col-sm-1"><label class="show-on-mob">All Other Accordions:</label>
									<div class="toggleSwitch">
										<input type="hidden" name="role_ticket[]" value="<?= in_array('all_access',$role_security) ? 'all_access' : '' ?>" class="toggle" data-toggle-value="all_access">
										<span style="<?= in_array('all_access',$role_security) ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg no-margin inline-img"></span>
										<span style="<?= in_array('all_access',$role_security) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg no-margin inline-img"></span>
									</div>
								</div>
								<div class="col-sm-1">
									<img class="inline-img" src="../img/remove.png" onclick="remRole(this);">
									<img class="inline-img" src="../img/icons/ROOK-add-icon.png" onclick="addRole();">
								</div>
							</div>
						</div>
					<?php } ?>
					<div class="form-group">
						<div class="col-sm-4">
							<!--<a href="" class="btn brand-btn">Back</a>-->
							<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>
						</div>
						<div class="col-sm-8">
							<span class="popover-examples list-inline pull-right" style="margin-left:-6em;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="The entire form will submit and close if this submit button is pressed.">
								<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed."  class="btn brand-btn">Submit</button></a></span>
							<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed."  class="btn brand-btn pull-right">Submit</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to create groups that will be used in <?= TICKET_TILE ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_groups" >
                        <?= TICKET_NOUN ?> Staff Groups<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_groups" class="panel-collapse collapse">
                <div class="panel-body">
					<script>
					function addStaffGroup() {
						var clone = $('.block-group').last().clone();
						clone.find('.form-group select').each(function() { removeGroupStaff(this); });
						clone.find('input[name="ticket_groups[]"]').val('Group #'+($('#collapse_groups .block-group').length+1));
						$('.block-group').last().after(clone);

						$('[name=staffid]').last().focus();
					}
					function addGroupStaff(img) {
						var group = $(img).closest('.block-group');
						var clone = group.find('.form-group').last().clone();
						resetChosen(clone.find("select[class*=chosen]"));
						group.append(clone);
					}
					function removeStaffGroup(img) {
						if($('.block-group').length <= 1) {
							addStaffGroup();
						}
						$(img).closest('.block-group').remove();
					}
					function removeGroupStaff(img) {
						if($(img).closest('.block-group').find('select').length <= 1) {
							addGroupStaff(img);
						}
						$(img).closest('.form-group').remove();
					}
					</script>
					<?php $groups = explode('#*#',mysqli_fetch_array(mysqli_query($dbc, "SELECT `value` FROM general_configuration WHERE `name`='ticket_groups'"))[0]);
					$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0 AND `show_hide_user`=1"));
					foreach($groups as $gid => $group) {
						$group = explode(',',$group);
						$group_name = 'Group #'.($gid+1);
						if(count($group) > 1 && !($group[0] > 0)) {
							$group_name = $group[0];
							unset($group[0]);
						} ?>
						<div class="block-group form-horizontal">
							<h4>Staff Group: <img src="../img/remove.png" class="inline-img" onclick="removeStaffGroup(this);"></h4>
							<input type="text" placeholder="Enter a name for the Group" name="ticket_groups[]" class="form-control" value="<?= $group_name ?>">
							<?php foreach($group as $staff) { ?>
								<div class="form-group">
									<label class="col-sm-3">Staff:</label>
									<div class="col-sm-8">
										<select name="ticket_groups[]" class="chosen-select-deselect">
											<option></option>
											<?php foreach($staff_list as $staff_option) { ?>
												<option <?= $staff == $staff_option['contactid'] ? 'selected' : '' ?> value="<?= $staff_option['contactid'] ?>"><?= $staff_option['first_name'].' '.$staff_option['last_name'] ?></option>
											<?php } ?>
										</select>
									</div>
									<div class="col-sm-1">
										<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addGroupStaff(this);">
										<img src="../img/remove.png" class="inline-img pull-right" onclick="removeGroupStaff(this);">
									</div>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<div class="form-group">
						<div class="col-sm-4">
							<!--<a href="" class="btn brand-btn">Back</a>-->
							<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>
						</div>
						<div class="col-sm-8">
							<span class="popover-examples list-inline pull-right" style="margin-left:-6em;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="The entire form will submit and close if this submit button is pressed.">
								<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed."  class="btn brand-btn">Submit</button></a></span>
							<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed."  class="btn brand-btn pull-right">Submit</button>
							<button class="btn brand-btn pull-right" onclick="addStaffGroup(); return false;">Add Staff Collaboration Group</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
				<div class="panel-heading">
						<h4 class="panel-title">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add or remove the status/heading you want attached to every <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field8" >
										<?= TICKET_TILE ?> Default Status<span class="glyphicon glyphicon-plus"></span>
								</a>
						</h4>
				</div>

				<div id="collapse_field8" class="panel-collapse collapse">
						<div class="panel-body">

								<div class="form-group">
										<label for="fax_number"	class="col-sm-4	control-label">Default Status:</label>
										<div class="col-sm-4">
											<?php $status_array = array('Sales/Estimate/RFP', 'Sidebar Needed', 'Strategy Needed', 'Information Gathering', 'Time Estimate Needed', 'To Be Scheduled',
																									'On Hold', 'Scheduled/To Do', 'Doing Today', 'Internal QA', 'QA Dev Needed', 'Customer QA', 'Waiting On Customer',
																									'Stopped Due To Customer', 'Accounting', 'Critical Incident', 'Review/Survey/Portfolio', 'Archive');
											?>
											<?php $status_db = mysqli_fetch_assoc(mysqli_query($dbc, "select value from general_configuration where name = 'ticket_default_status'")); ?>
											<select name="default_status" class="chosen-select-deselect form-control">
												<?php foreach($status_array as $status_value): ?>
													<?php if($status_db['value'] == $status_value): ?>
														<option selected="selected" value="<?php echo $status_value; ?>"><?php echo $status_value; ?></option>
													<?php else: ?>
														<option value="<?php echo $status_value; ?>"><?php echo $status_value; ?></option>
													<?php endif; ?>
												<?php endforeach; ?>
											</select>
										</div>
								</div>
								<div class="form-group">
									<div class="col-sm-4">
										<!--<a href="" class="btn brand-btn">Back</a>-->
										<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>
									</div>
									<div class="col-sm-8">
										<span class="popover-examples list-inline pull-right" style="margin-left:-6em;">
											<a data-toggle="tooltip" data-placement="top" title="" data-original-title="The entire form will submit and close if this submit button is pressed.">
											<button type="submit" name="submit" value="submit" class="btn brand-btn">Submit</button></a></span>
										<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed." class="btn brand-btn pull-right">Submit</button>
									</div>
								</div>
						</div>
				</div>
		</div>
  </div>

<div class="form-group">
	<div class="col-sm-4">
		<!--<a href="" class="btn brand-btn">Back</a>-->
		<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>
	</div>
	<div class="col-sm-8">
		<span class="popover-examples list-inline pull-right" style="margin-left:-6em;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="The entire form will submit and close if this submit button is pressed.">
			<button type="submit" name="submit" value="submit" class="btn brand-btn">Submit</button></a></span>
		<button type="submit" name="submit" value="submit" class="btn brand-btn pull-right">Submit</button>
	</div>
</div>

</form>
</div>
</div>

<?php include_once('../footer.php'); ?>
