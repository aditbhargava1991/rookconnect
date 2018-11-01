<?php
/*
Add Vendor
*/
include_once('../include.php');
include_once ('../Ticket/field_list.php');
error_reporting(0);

if($_GET['archive'] == 1) {
	$ticketid = $_GET['ticketid'];
        $date_of_archival = date('Y-m-d');
	$archive_query = "update tickets set deleted=1, `date_of_archival` = '$date_of_archival' where ticketid='$ticketid'";
	$delete_result = mysqli_query($dbc, $archive_query);
	header("Location: tickets.php");
}

$back_url = tile_visible($dbc, 'Tickets') > 0 ? WEBSITE_URL.'/Ticket/tickets.php' : WEBSITE_URL.'/home.php';
if(!empty($_GET['from'])) {
	echo '<input type="hidden" name="from" value="'.$_GET['from'].'">';
	$back_url = urldecode($_GET['from']);
}
if (isset($_POST['timer_add'])) {
    $url = 'add_tickets.php?ticketid='.$ticketid;
    echo '<script type="text/javascript"> window.location.replace("'.$url.'"); </script>';
}

if (isset($_POST['submit'])) {
	include('add_ticket_save.php');
}
?>
<script src="ticket.js"></script>
<script>
$(document).ready(function() {
	$(window).unload(function () {
	});
	window.onbeforeunload = function() {
		if($('#collapse_checkin [name=arrived][value=1],#tab_section_checkin [name=arrived][value=1]').length != $('#collapse_checkout [name=completed][value=1],#tab_section_checkout [name=completed][value=1]').length || $.inArray($('[name=timer]').val(),['',undefined]) < 0) {
			return "This <?= TICKET_NOUN ?> is currently active.";
		}
	}
});
var ticketid = 0;
var ticketid_list = [];
var ticket_wait = false;
var user_email = '<?= decryptIt($_SESSION['email_address']) ?>';
var user_id = '<?= $_SESSION['contactid'] ?>';
var from_url = '<?= urlencode($back_url) ?>';
var new_ticket_url = '<?= $_GET['new_ticket'] != 'true' && $_GET['ticketid'] > 0 ? '' : '&new_ticket=true' ?>';
var ticket_name = '<?= TICKET_NOUN ?>';
var folder_name = '<?= FOLDER_NAME ?>';
var tile_name = '<?= $_GET['tile_name'] ?>';
var staff_list = [];
var task_list = [];
var projectFilter = function() {}
var clientFilter = function() {}
var businessFilter = function() {}
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised(); ?>
<div class="container">
  <div class="row">
    <form id="add_ticket_form" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <?php $value_config = get_field_config($dbc, 'tickets');
    	$sort_order = explode(',',get_config($dbc, 'ticket_sortorder'));
		$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"));

        $clientid = '';
        $businessid = '';
		$heading_auto = 1;
        if(!empty($_GET['supportid'])) {
            $supportid = $_GET['supportid'];
            $company_name = get_support($dbc, $supportid, 'company_name');
            $get_contact =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contactid FROM	contacts WHERE	name='$company_name'"));
            $businessid = $get_contact['contactid'];
            $heading = get_support($dbc, $supportid, 'heading');
			$heading_auto = 1;
            $assign_work = get_support($dbc, $supportid, 'message');
            $status = 'Time Estimate Needed';
            echo '<input type="hidden" name="supportid" id="supportid" value="'.$supportid.'">';
        } else {
            echo '<input type="hidden" name="supportid" id="supportid" value="0">';
        }
        if(!empty($_GET['bid'])) {
            $businessid = $_GET['bid'];
        }
        if(!empty($_GET['clientid'])) {
            $clientid = $_GET['clientid'];
            $businessid = get_contact($dbc, $clientid, 'businessid');
        }
        if(!empty($_GET['projectid'])) {
            $projectid = $_GET['projectid'];
            $businessid = get_project($dbc, $projectid, 'businessid');
            $clientid = get_project($dbc, $projectid, 'clientid');
            $project_path = get_project($dbc, $projectid, 'project_path');
            $project_lead = get_project($dbc, $projectid, 'project_lead');
        }
		if(!empty($_GET['milestone_timeline'])) {
			$milestone_timeline = str_replace(['FFMSPACE','FFMEND','FFMHASH'], [' ','&','#'], urldecode($_GET['milestone_timeline']));
		}

        $contactid = $_SESSION['contactid'];
        if(!empty($_GET['contactid'])) {
            $contactid = ','.$_GET['contactid'].',';
        }
        if(!empty($_GET['startdate'])) {
            $to_do_date = $_GET['startdate'];
        }
        if(!empty($_GET['enddate'])) {
            $to_do_end_date = $_GET['enddate'];
        }
        if(!empty($_GET['starttime'])) {
            $to_do_start_time = $_GET['starttime'];
        }
        if(!empty($_GET['endtime'])) {
            $to_do_end_time = $_GET['endtime'];
        }

        if(!empty($_GET['ticketid'])) {

            $ticketid = $_GET['ticketid'];
            $get_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM tickets WHERE ticketid='$ticketid'"));
			foreach($get_ticket as $field_id => $value) {
				if($value == '0000-00-00' || $value == '0') {
					$get_ticket[$field_id] = '';
				}
			}

			$ticket_type = $get_ticket['ticket_type'];
            $businessid = $get_ticket['businessid'];
            $equipmentid = $get_ticket['equipmentid'];

            $clientid = $get_ticket['clientid'];
            if($businessid == '') {
                $businessid = get_contact($dbc, $clientid, 'businessid');
            }

            $projectid = $get_ticket['projectid'];
            $client_projectid = $get_ticket['client_projectid'];
            $piece_work = $get_ticket['piece_work'];
            $service_type = $get_ticket['service_type'];
            $service = $get_ticket['service'];
            $sub_heading = $get_ticket['sub_heading'];
            $heading = $get_ticket['heading'];
			$heading_auto = $get_ticket['heading_auto'];
            $category = $get_ticket['category'];
            $assign_work = $get_ticket['assign_work'];
			$project_path = '';
			if(!empty($projectid)) {
                $project_path = get_project($dbc, $projectid, 'project_path');
			} else if(!empty($client_projectid)) {
				$project_path = get_client_project($dbc, $client_projectid, 'project_path');
			}

            $projecttype = get_project($dbc, $projectid, 'projecttype');
            $milestone_timeline = html_entity_decode($get_ticket['milestone_timeline']);

            $created_date = date('Y-m-d');
            $login_id = $_SESSION['contactid'];
            // AND timer_type='Break' AND end_time IS NULL

            $get_ticket_timer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT start_timer_time, timer_type FROM ticket_timer WHERE tickettimerid IN (SELECT MAX(`tickettimerid`) FROM `ticket_timer` WHERE `ticketid`='$ticketid' AND created_by='$login_id')"));

            $created_date = $get_ticket['created_date'];
            $created_by = $get_ticket['created_by'];

            $start_time = $get_ticket_timer['start_timer_time'];
            $timer_type = $get_ticket_timer['timer_type'];

            if($start_time == '0' || $start_time == '') {
                $time_seconds = 0;
            } else {
                $time_seconds = (time()-$start_time);
            }

            $to_do_date = $get_ticket['to_do_date'];
            $internal_qa_date = $get_ticket['internal_qa_date'];
            $deliverable_date = $get_ticket['deliverable_date'];

            $to_do_end_date = $get_ticket['to_do_end_date'];
            $internal_qa_contactid = $get_ticket['internal_qa_contactid'];
            $deliverable_contactid = $get_ticket['deliverable_contactid'];

            $to_do_start_time = $get_ticket['to_do_start_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['to_do_start_time']));
            $to_do_end_time = $get_ticket['to_do_end_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['to_do_end_time']));
            $internal_qa_start_time = $get_ticket['internal_qa_start_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['internal_qa_start_time']));
            $internal_qa_end_time = $get_ticket['internal_qa_end_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['internal_qa_end_time']));
            $deliverable_start_time = $get_ticket['deliverable_start_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['deliverable_start_time']));
            $deliverable_end_time = $get_ticket['deliverable_end_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['deliverable_end_time']));

            $status = $get_ticket['status'];
            $max_time = explode(':', $get_ticket['max_time']);
            $max_qa_time = explode(':', $get_ticket['max_qa_time']);
            $spent_time = $get_ticket['spent_time'];
            $total_days = $get_ticket['total_days'];
            $contactid = $get_ticket['contactid'];
            echo '<span>Created By '.get_staff($dbc, $created_by).' On '.$created_date.'';
            //include ('add_ticket_timer.php');
            echo '</span><br><br><br>'; ?>
			<input type="hidden" class="start_time" value="<?php echo $time_seconds ?>">
			<input type="hidden" id="login_contactid" value="<?php echo $_SESSION['contactid'] ?>" />
			<input type="hidden" id="timer_type" value="<?php echo $timer_type ?>" />
        <?php } else if(!empty($_GET['type'])) {
            $ticket_type = filter_var($_GET['type'],FILTER_SANITIZE_STRING);
        } ?>
		<input type="hidden" id="ticketid" name="ticketid" value="<?php echo $ticketid ?>" />

		<?php //Get Ticket Type Fields
		if(!empty($ticket_type)) {
			$value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
			$sort_order = explode(',',get_config($dbc, 'ticket_sortorder_'.$ticket_type));
		}

		//Accordion Sort Order
		foreach ($accordion_list as $accordion_field) {
			if(!in_array($accordion_field, $sort_order)) {
				$sort_order[] = $accordion_field;
			}
		}

		//Apply Templates
		if(strpos($value_config,',TEMPLATE Work Ticket') !== FALSE) {
			$value_config = ',Information,PI Business,PI Name,PI Project,PI AFE,PI Sites,Staff,Staff Position,Staff Hours,Staff Overtime,Staff Travel,Staff Subsistence,Services,Service Category,Equipment,Materials,Material Quantity,Material Rates,Purchase Orders,Notes,';
		}

		//Check if only using today's data
		$query_daily = "";
		if(strpos($value_config,',Time Tracking Current,') !== FALSE) {
			$query_daily = " AND `date_stamp`='".date('Y-m-d')."' ";
		}

		//Get Security Permissions
		$ticket_roles = explode('#*#',get_config($dbc, 'ticket_roles'));
		$ticket_role = mysqli_query($dbc, "SELECT `position` FROM `ticket_attached` WHERE `src_table`='Staff' AND `position`!='' AND `item_id`='".$_SESSION['contactid']."' AND `ticketid`='$ticketid' AND `ticketid` > 0 $query_daily");
		$access_any = (vuaed_visible_function($dbc, 'ticket') + vuaed_visible_function($dbc, 'ticket_type_'.$get_ticket['ticket_type'])) > 0;
		if($get_ticket['status'] == 'Archive') {
			$access_project = false;
			$access_staff = false;
			$access_contacts = false;
			$access_waitlist = false;
			$access_staff_checkin = false;
			$access_all_checkin = false;
			$access_medication = check_subtab_persmission($dbc, 'ticket', ROLE, 'medication');
			$access_complete = false;
			$access_all = false;
			$access_any = false;
		} else if(config_visible_function($dbc, 'ticket') > 0) {
			$access_project = check_subtab_persmission($dbc, 'ticket', ROLE, 'project');
			$access_staff = check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_list');
			$access_contacts = check_subtab_persmission($dbc, 'ticket', ROLE, 'contact_list');
			$access_waitlist = check_subtab_persmission($dbc, 'ticket', ROLE, 'wait_list');
			$access_staff_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_checkin');
			$access_all_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_checkin');
			$access_medication = check_subtab_persmission($dbc, 'ticket', ROLE, 'medication');
			$access_complete = check_subtab_persmission($dbc, 'ticket', ROLE, 'complete');
			$access_all = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_access');
		} else if((count($ticket_roles) > 1 || explode('|',$ticket_roles[0])[0] != '') && mysqli_num_rows($ticket_role) > 0) {
			$ticket_role = html_entity_decode(mysqli_fetch_assoc($ticket_role)['position']);
			foreach($ticket_roles as $ticket_role_level) {
				$ticket_role_level = explode('|',html_entity_decode($ticket_role_level));
				if($ticket_role_level[0] > 0) {
					$ticket_role_level[0] = get_positions($dbc, $ticket_role_level[0], 'name');
				}
				if($ticket_role_level[0] == $ticket_role) {
					$access_project = in_array('project',$ticket_role_level);
					$access_staff = in_array('staff_list',$ticket_role_level);
					$access_contacts = in_array('contact_list',$ticket_role_level);
					$access_waitlist = in_array('wait_list',$ticket_role_level);
					$access_staff_checkin = in_array('staff_checkin',$ticket_role_level);
					$access_all_checkin = in_array('all_checkin',$ticket_role_level);
					$access_medication = in_array('medication',$ticket_role_level);
					$access_complete = in_array('complete',$ticket_role_level);
					$access_all = in_array('ticket',$ticket_role_level);
				}
			}
		} else if(count(array_filter($arr, function ($var) { return (strpos($var, 'default') !== false); })) > 0) {
			foreach($ticket_roles as $ticket_role_level) {
				$ticket_role_level = explode('|',$ticket_role_level);
				if(in_array('default',$ticket_role_level)) {
					$access_project = in_array('project',$ticket_role_level);
					$access_staff = in_array('staff_list',$ticket_role_level);
					$access_contacts = in_array('contact_list',$ticket_role_level);
					$access_waitlist = in_array('wait_list',$ticket_role_level);
					$access_staff_checkin = in_array('staff_checkin',$ticket_role_level);
					$access_all_checkin = in_array('all_checkin',$ticket_role_level);
					$access_medication = in_array('medication',$ticket_role_level);
					$access_complete = in_array('complete',$ticket_role_level);
					$access_all = in_array('ticket',$ticket_role_level);
				}
			}
		} else {
			$access_project = check_subtab_persmission($dbc, 'ticket', ROLE, 'project');
			$access_staff = check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_list');
			$access_contacts = check_subtab_persmission($dbc, 'ticket', ROLE, 'contact_list');
			$access_waitlist = check_subtab_persmission($dbc, 'ticket', ROLE, 'wait_list');
			$access_staff_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_checkin');
			$access_all_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_checkin');
			$access_medication = check_subtab_persmission($dbc, 'ticket', ROLE, 'medication');
			$access_complete = check_subtab_persmission($dbc, 'ticket', ROLE, 'complete');
			$access_all = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_access');
		} ?>
		<input type="hidden" name="no_time_sheet" value="<?= strpos($value_config, ',No Track Time Sheets,') !== FALSE ? 1 : 0 ?>">
        <h1 class=""><span class="ticketid_span"><?php echo (empty($_GET['ticketid']) ? 'New '.TICKET_NOUN : get_ticket_label($dbc, $get_ticket)); ?></span>
            <?php if(!empty($_GET['ticketid']) && strpos($value_config, ','."Timer".',') !== FALSE) : ?>
            <a class="btn brand-btn" data-toggle="collapse" data-parent="#accordion2" href="#collapse_timer" >
               Start <?= TICKET_NOUN ?> Tracking
            </a>
            <?php endif; ?>
			<?php if(search_visible_function($dbc, 'ticket')) { ?>
				<div class="pull-right">
					<span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to return to the ticket list."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a href="tickets.php" class="btn brand-btn"><?= TICKET_NOUN ?> List</a>
				</div>
			<?php } ?>
        </h1>
		<div class="gap-top double-gap-bottom"><a href="<?php echo $back_url; ?>" class="btn config-btn">Back to Dashboard</a></div>

        <div class="panel-group" id="accordion2">

        	<?php foreach($sort_order as $sort_field) { ?>

	            <?php if (strpos($value_config, ','."Information".',') !== FALSE && $sort_field == 'Information') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add any necessary project information. This will group your <?= TICKET_TILE ?> under a certain project, under a certain customer."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info">
								<?= PROJECT_NOUN ?> Information<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_info" class="panel-collapse collapse in">
	                    <div class="panel-body">
	                        <?php include ('add_project_info.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Details".',') !== FALSE && $sort_field == 'Details') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add any necessary project information. This will group your <?= TICKET_TILE ?> under a certain project, under a certain customer."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_details">
								<?= PROJECT_NOUN ?> Details<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_details" class="panel-collapse collapse">
	                    <div class="panel-body">
	                        <?php include ('add_project_details.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Path & Milestone".',') !== FALSE && $sort_field == 'Path & Milestone') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
	                        <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to attach the milestone to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pm">Project Path & Milestone<span class="glyphicon glyphicon-plus"></span></a>
	                    </h4>
	                </div>

	                <div id="collapse_pm" class="panel-collapse collapse">
	                    <div class="panel-body">
	                        <?php include ('add_ticket_path_milestone.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Individuals".',') !== FALSE && $sort_field == 'Individuals') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add any necessary project information. This will group your <?= TICKET_TILE ?> under a certain project, under a certain customer."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_individuals">Individuals Present<span class="glyphicon glyphicon-plus"></span></a>
	                    </h4>
	                </div>

	                <div id="collapse_individuals" class="panel-collapse collapse">
	                    <div class="panel-body">
	                        <?php include ('add_ticket_individuals.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Fees".',') !== FALSE && $sort_field == 'Fees') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
	                        <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to attach fees to this ticket."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_fees">Fees<span class="glyphicon glyphicon-plus"></span></a>
	                    </h4>
	                </div>

	                <div id="collapse_fees" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include('add_ticket_fees.php') ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if ((strpos($value_config, ','."Location".',') !== FALSE || strpos($value_config, ','."Emergency".',') !== FALSE) && $sort_field == 'Location') { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to specify the site for this ticket."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_locations">Site<span class="glyphicon glyphicon-plus"></span></a>
							</h4>
						</div>

						<div id="collapse_locations" class="panel-collapse collapse">
							<div class="panel-body">
								<?php include('add_ticket_location.php') ?>
							</div>
						</div>
					</div>
	            <?php } ?>

	            <?php if ((strpos($value_config, ','."Mileage".',') !== FALSE || strpos($value_config, ','."Drive Time".',') !== FALSE) && $sort_field == 'Mileage') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to record Mileage for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_mileage" >
	                           <?= strpos($value_config, ','."Mileage".',') !== FALSE ? 'Mileage' : 'Drive Time' ?><span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_mileage" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include('add_ticket_mileage.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

				<?php if(strpos($value_config, ',Staff,') !== FALSE && $sort_field == 'Staff') {
					$roles = explode('#*#',get_config($dbc,"ticket_roles")); ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to attach multiple Staff to this ticket."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff">
								   Staff<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_staff" class="panel-collapse collapse">
							<div class="panel-body">
								<?php include('add_ticket_staff_list.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if(strpos($value_config, ',Staff Tasks,') !== FALSE && $sort_field == 'Staff Tasks') {
				if($ticketid > 0 && $_GET['new_ticket'] != 'true') { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to attach multiple Staff to this ticket."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff_task">
								   Staff Tasks<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_staff_task" class="panel-collapse collapse">
							<div class="panel-body">
							<?php include('add_ticket_staff_tasks.php'); ?>
							</div>
						</div>
					</div>
				<?php }
				if(strpos($value_config, ',Task Extra Billing,') !== FALSE && $access_any == true && $sort_field == 'Staff Tasks') { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to assign the tasks that will be available for Staff."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff_tasks">
								   Assigned Tasks<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_staff_tasks" class="panel-collapse collapse">
							<div class="panel-body">
							<?php include('add_ticket_staff_assign_tasks.php'); ?>
							</div>
						</div>
					</div>
				<?php }
				} ?>

				<?php if(strpos($value_config, ',Members,') !== FALSE && $sort_field == 'Members') { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to attach multiple Members to this ticket."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_members">
								   Members<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_members" class="panel-collapse collapse">
							<div class="panel-body">
								<?php include('add_ticket_members.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if(strpos($value_config, ',Clients,') !== FALSE && $sort_field == 'Clients') { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to attach multiple Clients to this ticket."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_client">
								   Clients<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_client" class="panel-collapse collapse">
							<div class="panel-body">
								<?php include('add_ticket_clients.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if(strpos($value_config, ',Wait List,') !== FALSE && $sort_field == 'Wait List') { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to attach multiple additional individuals to this ticket as a Wait List."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_waitlist">
								   Wait List<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_waitlist" class="panel-collapse collapse">
							<div class="panel-body">
								<?php include('add_ticket_wait_list.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>

	            <?php if (strpos($value_config, ','."Check In".',') !== FALSE && $sort_field == 'Check In') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to record Check Ins for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_checkin" >
	                           Check In<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_checkin" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include('add_ticket_checkin.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Medication".',') !== FALSE && $access_medication === TRUE && $sort_field == 'Medication') {
					$member_list = sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category`='Members' AND `deleted`=0 AND `status`>0 AND `contactid` IN (SELECT `item_id` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='Members')")); ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to record Medication Administration for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_medication" >
								   Medication Administration<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_medication" class="panel-collapse collapse">
							<div class="panel-body">
								<?php include('add_ticket_medications.php'); ?>
							</div>
						</div>
					</div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Ticket Details".',') !== FALSE && $sort_field == 'Ticket Details') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add Services for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ticket_details" >
	                           <?= TICKET_NOUN ?> Details<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_ticket_details" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include ('add_ticket_info.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Services".',') !== FALSE && $sort_field == 'Ticket Details') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add Services for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_services" >
	                           Services<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_services" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include ('add_ticket_info.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Equipment".',') !== FALSE && $sort_field == 'Equipment') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add Equipment for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_equipment" >
	                           Equipment<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_equipment" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include('add_ticket_equipment.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Checklist".',') !== FALSE && $access_all > 0 && $sort_field == 'Checklist') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to create Checklists for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_checklist" >
	                           Checklist<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_checklist" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include('add_ticket_checklist.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Checklist Items".',') !== FALSE && $access_all > 0 && $sort_field == 'Checklist Items') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to create Checklists for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_checklist_view" >
	                           Checklists<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_checklist_view" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include('add_ticket_view_checklist.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Safety".',') !== FALSE && $access_all > 0 && $sort_field == 'Safety') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to review Safety Documents for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_safety" >
	                           Safety<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_safety" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include('add_ticket_safety.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Materials".',') !== FALSE && $sort_field == 'Materials') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add Materials for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_materials" >
	                           Materials<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_materials" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include('add_ticket_materials.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Inventory".',') !== FALSE && $sort_field == 'Inventory') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add Inventory for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_inventory" >
	                           Inventory<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_inventory" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include('add_ticket_inventory.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Purchase Orders".',') !== FALSE && $access_all > 0 && $sort_field == 'Purchase Orders') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to create Purchase Orders for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_purchase_orders" >
	                           Purchase Orders<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_purchase_orders" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include('add_ticket_purchase_orders.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Delivery".',') !== FALSE && $sort_field == 'Delivery') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to set the deliverables for the Work Order."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_delivery" >
	                           Delivery Details<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_delivery" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include('add_ticket_delivery.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Transport".',') !== FALSE && $sort_field == 'Transport') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to set the deliverables for the Work Order."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_transport" >
	                           Transport Log<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_transport" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include('add_ticket_transport_log.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Documents".',') !== FALSE && $sort_field == 'Documents') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to attach any links and attachments to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_doc">
							   Documents<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_doc" class="panel-collapse collapse">
	                    <div class="panel-body">
	                    <?php include ('add_view_ticket_documents.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Check Out".',') !== FALSE && $sort_field == 'Check Out') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to record Check Ins for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_checkout" >
	                           Check Out<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_checkout" class="panel-collapse collapse">
	                    <div class="panel-body">
							<?php include('add_ticket_checkout.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if ((strpos($value_config, ','."Deliverables".',') !== FALSE || strpos($value_config, ','."Deliverable To Do".',') !== FALSE || strpos($value_config, ','."Deliverable Internal".',') !== FALSE || strpos($value_config, ','."Deliverable Customer".',') !== FALSE) && $sort_field == 'Deliverables') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
							<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to set the deliverables for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_deli" >
	                           Deliverables<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_deli" class="panel-collapse collapse">
	                    <div class="panel-body">
	                        <?php include ('add_view_ticket_deliverables.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

	            <?php if (strpos($value_config, ','."Time Tracking Block".',') !== FALSE && $sort_field == 'Timer') { ?>
	            <div class="panel panel-default">
	                <div class="panel-heading">
	                    <h4 class="panel-title">
	                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_timer" >
	                           Time Tracking<span class="glyphicon glyphicon-plus"></span>
	                        </a>
	                    </h4>
	                </div>

	                <div id="collapse_timer" class="panel-collapse collapse">
	                    <div class="panel-body">
	                        <?php include ('add_view_ticket_timer.php'); ?>
	                    </div>
	                </div>
	            </div>
	            <?php } ?>

				<?php if (strpos($value_config, ','."Day Tracking Block".',') !== FALSE && $access_all > 0 && $sort_field == 'Timer') { ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_dayt" >
							   Day Tracking<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_dayt" class="panel-collapse collapse">
						<div class="panel-body">
							<?php include ('add_view_day_tracking.php'); ?>
						</div>
					</div>
				</div>
				<?php } ?>

				<?php if (strpos($value_config, ','."Addendum".',') !== FALSE && $sort_field == 'Addendum') {
					$comment_type = 'addendum'; ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_addendum" >
								   Addendum<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_addendum" class="panel-collapse collapse">
							<div class="panel-body">
							 <?php include ('add_view_ticket_comment.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if (strpos($value_config, ','."Client Log".',') !== FALSE && $sort_field == 'Client Log') {
					$comment_type = 'client_log'; ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_client_log" >
								   Staff Log Notes<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_client_log" class="panel-collapse collapse">
							<div class="panel-body">
							 <?php include ('add_ticket_log_notes.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if (strpos($value_config, ','."Debrief".',') !== FALSE && $sort_field == 'Debrief') {
					$comment_type = 'debrief'; ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_debrief" >
								   Debrief<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_debrief" class="panel-collapse collapse">
							<div class="panel-body">
								<?php include ('add_view_ticket_comment.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if (strpos($value_config, ','."Member Log Notes".',') !== FALSE && $sort_field == 'Member Log Notes') {
					$category = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category` FROM `contacts` WHERE `category` NOT IN (".STAFF_CATS.",'Business','Sites') AND `deleted`=0 AND `status`>0 GROUP BY `category` ORDER BY COUNT(*) DESC"))['category'];
					$comment_type = 'member_note'; ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_daily_log_notes" >
								   <?= $category ?> Specific Daily Log Notes<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_daily_log_notes" class="panel-collapse collapse">
							<div class="panel-body">
								<?php include ('add_view_ticket_comment.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if (strpos($value_config, ','."Cancellation".',') !== FALSE && $sort_field == 'Cancellation') { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_cancel" >
								   Cancellation<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_cancel" class="panel-collapse collapse">
							<div class="panel-body">
								<?php include('add_ticket_cancellation.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if (strpos($value_config, ','."Custom Notes".',') !== FALSE && $sort_field == 'Custom Notes') { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_custom_notes" >
								   <?= get_config($dbc, 'ticket_custom_notes_heading') ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_custom_notes" class="panel-collapse collapse">
							<div class="panel-body">
								<?php foreach(explode('#*#',get_config($dbc, 'ticket_custom_notes_type')) as $comment_type) {
									echo "<h3>$comment_type</h3>";
									$comment_type = config_safe_str($comment_type);
									include('add_view_ticket_comment.php');
								} ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if (strpos($value_config, ','."Notes".',') !== FALSE && $sort_field == 'Notes') {
					$comment_type = 'note'; ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ticket_notes" >
								   Notes<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_ticket_notes" class="panel-collapse collapse">
							<div class="panel-body">
							 <?php include ('add_view_ticket_comment.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if ((strpos($value_config, ','."Summary".',') !== FALSE || strpos($value_config, ','."Staff Summary".',') !== FALSE) && $sort_field == 'Summary') { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_summary" >
								   <?= strpos($value_config, ','."Staff Summary".',') !== FALSE ? 'Staff Summary' : 'Summary' ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_summary" class="panel-collapse collapse">
							<div class="panel-body">
								<?php include('add_ticket_summary.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if (strpos($value_config, ','."Multi-Disciplinary Summary Report".',') !== FALSE && $sort_field == 'Multi-Disciplinary Summary Report') {
					$comment_type = 'note'; ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_mdsr" >
								   Multi-Disciplinary Summary Report<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_mdsr" class="panel-collapse collapse">
							<div class="panel-body">
							 <?php include ('add_view_multi_disciplinary_summary_report.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<?php if (strpos($value_config, ','."Complete".',') !== FALSE && $sort_field == 'Complete') {
					$comment_type = 'note'; ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_complete" >
								   Complete <?= TICKET_NOUN ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_complete" class="panel-collapse collapse">
							<div class="panel-body">
								<?php include('add_ticket_complete.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>

	            <?php if (strpos($value_config, ','."Notifications".',') !== FALSE && $sort_field == 'Notifications') { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_notifications" >
								   Notifications<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_notifications" class="panel-collapse collapse">
							<div class="panel-body">
						        <?php include ('add_view_ticket_notifications.php'); ?>
							</div>
						</div>
					</div>
	        	<?php } ?>

	            <?php if (strpos($value_config, ','."Region Location Classification".',') !== FALSE && $sort_field == 'Region Location Classification') { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_reglocclass" >
								   Region/Location/Classification<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_reglocclass" class="panel-collapse collapse">
							<div class="panel-body">
						        <?php include ('add_ticket_reg_loc_class.php'); ?>
							</div>
						</div>
					</div>
	        	<?php } ?>

	            <?php if (strpos($value_config, ','."Incident Reports".',') !== FALSE && $sort_field == 'Incident Reports') { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_incident_reports" >
								   <?= INC_REP_TILE ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_incident_reports" class="panel-collapse collapse">
							<div class="panel-body">
						        <?php include ('add_view_ticket_incident_reports.php'); ?>
							</div>
						</div>
					</div>
	        	<?php } ?>

	        <?php } ?>
        </div>

        <div class="form-group">
			<p><span class="hp-red"><em>Required Fields *</em></span></p>
        </div>

		<div id="no-more-tables" class="connected_table"></div>
		<div class="gap-top double-gap-bottom">
			<a href="index.php<?= (empty($_GET['tile_name']) ? '' : '?tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '?tile_group='.$_GET['tile_group']) ?>" class="pull-right btn brand-btn" onclick="<?= (strpos($value_config, ','."Timer".',') !== FALSE) ? 'stopTimers();' : '' ?><?= (strpos($value_config, ','."Check Out".',') !== FALSE) ? 'checkoutAll();' : '' ?>">Finish</a>
			<a href="<?php echo addOrUpdateCurrentUrlParam(array('archive'), array('1')); ?>" class="pull-right gap-right"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" alt="Delete" width="36" /></a>
			<?php if(strpos($value_config,',Additional,') !== FALSE) { ?>
				<a href="add_tickets.php?addition_to=current_ticket" class="pull-right addition_button btn brand-btn" onclick="return addition();">Additional</a>
			<?php } ?>
			<?php if(strpos($value_config,',Multiple,') !== FALSE) { ?>
				<a href="add_tickets.php?addition_to=current_ticket" class="pull-right multiple_button btn brand-btn" onclick="return multiple_tickets($('[name=multiple_ticket_count]').val(), ticketid);">Multiple <?= TICKET_TILE ?></a>
				<div class="col-sm-1 pull-right"><input type="number" value="1" min="1" step="1" class="form-control" name="multiple_ticket_count"></div>
			<?php } ?>
			<?php if(strpos($value_config,',Export Ticket Log,') !== FALSE && !empty($ticketid)) {
				$ticket_log_template = !empty(get_config($dbc, 'ticket_log_template')) ? get_config($dbc, 'ticket_log_template') : 'template_a'; ?>
				<a href="../Ticket/ticket_log_templates/<?= $ticket_log_template ?>_pdf.php?ticketid=<?= $ticketid ?>" target="_blank" class="pull-right btn brand-btn">Export <?= TICKET_NOUN ?> Log</a>
			<?php } ?>
			<div class="clearfix"></div>
		</div>

    </form>
	<img class="no-toggle statusIcon float-bottom-left no-margin inline-img text-lg" title="" src="" />

  </div>
</div>
<script>
var setHeading = function() {
	if(ticketid > 0) {
	<?php if(strpos($value_config, ','."Heading Business Invoice".',') !== false) { ?>
		if($('[name=heading_auto]').val() == 1) {
			var business = $('[name=businessid] option:selected').first().text();
			var invoice = $('[name=salesorderid]').first().val();
			$('[name=heading]').val(business+' - '+invoice).change();
		}
	<?php } else if(strpos($value_config, ','."Heading Bus Invoice Date".',') !== false) { ?>
		if($('[name=heading_auto]').val() == 1) {
			var business = $('[name=businessid] option:selected').first().text();
			var invoice = $('[name=salesorderid]').first().val();
			var date = $('[name=to_do_date]').first().val();
			$('[name=heading]').val(invoice+' - '+business+': '+date).change();
		}
	<?php } else if(strpos($value_config, ','."Heading Date".',') !== false) { ?>
		if($('[name=heading_auto]').val() == 1) {
			var date = $('[name=to_do_date]').first().val();
			$('[name=heading]').val(date).change();
		}
	<?php } else if(strpos($value_config, ','."Heading Business Date".',') !== false) { ?>
		if($('[name=heading_auto]').val() == 1) {
			var business = $('[name=businessid] option:selected').first().text();
			var date = $('[name=to_do_date]').first().val();
			$('[name=heading]').val(business+' - '+date).change();
		}
	<?php } else if(strpos($value_config, ','."Heading Contact Date".',') !== false) { ?>
		if($('[name=heading_auto]').val() == 1) {
			var contact = $('[name=clientid] option:selected').first().text();
			var date = $('[name=to_do_date]').first().val();
			$('[name=heading]').val(contact+' - '+date).change();
		}
	<?php } else if(strpos($value_config, ','."Heading Business".',') !== false) { ?>
		if($('[name=heading_auto]').val() == 1) {
			var business = $('[name=businessid] option:selected').first().text();
			$('[name=heading]').val(business).change();
		}
	<?php } else if(strpos($value_config, ','."Heading Contact".',') !== false) { ?>
		if($('[name=heading_auto]').val() == 1) {
			var contact = $('[name=clientid] option:selected').first().text();
			$('[name=heading]').val(contact).change();
		}
	<?php } else if(strpos($value_config, ','."Heading Milestone Date".',') !== false) { ?>
		if($('[name=heading_auto]').val() == 1) {
			var milestone = $('[name=milestone_timeline] option:selected').text();
			var date = $('[name=to_do_date]').first().val();
			$('[name=heading]').val(milestone+': '+invoice).change();
		}
	<?php } else if(strpos($value_config, ','."Heading Assigned".',') !== false) { ?>
		if($('[name=heading_auto]').val() == 1) {
			var assigned = $('[name=contactid] option:selected,[name=item_id][data-type=Staff] option:selected').first().text();
			$('[name=heading]').val(assigned).change();
		}
	<?php } ?>
	} else { setTimeout(setHeading, 250); }
}
</script>
<?php include_once('../footer.php'); ?>>
