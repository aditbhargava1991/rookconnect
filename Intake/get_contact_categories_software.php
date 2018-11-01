<?php
/*
 * iFrame content to select Contact category
 */
include ('../include.php');
checkAuthorised('intake');
error_reporting(0);

if(isset($_POST['complete_form'])) {
	echo '<h1>Redirecting...</h1>';
	$action = $_POST['action'];
	$intakeid = $_POST['intakeid'];
	$assigned_date = date('Y-m-d');
	$category_arr = explode('*#*', $_POST['contact_category']);
	$src_table = $category_arr[0];
	$category = $category_arr[1];
	$contactid = $_POST['contact_list'];
	$contact_update_info = $_POST['contact_update_info'];
	$intake_type = $_POST['intake_type'];
	$new_injury = $_POST['new_injury'];

	if($action == 'project') {
		$projecttype = $_POST['projecttype'];
		$projectid = $_POST['projectid'];
		if($projectid == 'NEW_PROJECT') {
			$project_name = $_POST['project_name'];

			mysqli_query($dbc, "INSERT INTO `project` (`projecttype`, `project_name`,`created_date`,`created_by`) VALUES ('$projecttype', '$project_name','".date('Y-m-d')."','".$_SESSION['contactid']."')");
			$projectid = mysqli_insert_id($dbc);
		}
        $milestone = filter_var($_POST['milestone'],FILTER_SANITIZE_STRING);

		$before_change = capture_before_change($dbc, 'intake', 'projectid', 'intakeid', $intakeid);
		$before_change .= capture_before_change($dbc, 'intake', 'assigned_date', 'intakeid', $intakeid);
		mysqli_query($dbc, "UPDATE `intake` SET `projectid` = '$projectid', `project_milestone`='$milestone', `assigned_date` = '$assigned_date' WHERE `intakeid` = '$intakeid'");
		$history = capture_after_change('projectid', $projectid);
		$history .= capture_after_change('assigned_date', $assigned_date);
        add_update_history($dbc, 'intake_history', $history, '', $before_change);
		$echo_script = '<script type="text/javascript"> parent.window.location.href = "'.WEBSITE_URL.'/Project/projects.php?edit='.$projectid.'"; </script>';
	}

	if($action == 'ticket') {
		$ticket_type = $_POST['ticket_type'];
		$ticketid = $_POST['ticketid'];
		if($ticketid == 'NEW_TICKET') {
			$ticket_name = $_POST['ticket_name'];

			mysqli_query($dbc, "INSERT INTO `tickets` (`ticket_type`, `heading`, `created_by`) VALUES ('$ticket_type', '$ticket_name', '".$_SESSION['contactid']."')");
			$ticketid = mysqli_insert_id($dbc);
		}

		$intake = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake` WHERE `intakeid` = '$intakeid'"));
		mysqli_query($dbc, "UPDATE `tickets` SET `assign_work` = CONCAT(IFNULL(`assign_work`,''),'".$intake['ticket_description']."') WHERE `ticketid` = '$ticketid'");

		$before_change = capture_before_change($dbc, 'intake', 'ticketid', 'intakeid', $intakeid);
		$before_change .= capture_before_change($dbc, 'intake', 'assigned_date', 'intakeid', $intakeid);

		mysqli_query($dbc, "UPDATE `intake` SET `ticketid` = '$ticketid', `assigned_date` = '$assigned_date' WHERE `intakeid` = '$intakeid'");

		$history = capture_after_change('ticketid', $ticketid);
		$history .= capture_after_change('assigned_date', $assigned_date);
	  add_update_history($dbc, 'intake_history', $history, '', $before_change);
		$echo_script = '<script type="text/javascript"> parent.window.location.href = "'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticketid.'"; </script>';
	}

	if($action == 'sales') {
		$salesid = $_POST['salesid'];
		if($salesid == 'NEW_SALES') {
			$primary_staff = filter_var($_POST['sales_primary_staff'],FILTER_SANITIZE_STRING);
			$share_lead = filter_var(implode(',', $_POST['sales_share_lead']),FILTER_SANITIZE_STRING);
			$status = !empty(get_config($dbc, 'lead_status_default')) ? get_config($dbc, 'lead_status_default') : (!empty(array_filter(explode(',',get_config($dbc, 'sales_lead_status')))[0]) ? array_filter(explode(',',get_config($dbc, 'sales_lead_status')))[0] : 'Pending');
			mysqli_query($dbc, "INSERT INTO `sales` (`primary_staff`, `share_lead`, `created_date`,`lead_created_by`, `status`) VALUES ('$primary_staff', '$share_lead', '".date('Y-m-d')."','".get_contact($dbc, $_SESSION['contactid'])."', '$status')");
			$salesid = mysqli_insert_id($dbc);
		}
		mysqli_query($dbc, "UPDATE `intake` SET `salesid` = '$salesid', `assigned_date` = '$assigned_date' WHERE `intakeid` = '$intakeid'");

		$intake = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake` WHERE `intakeid` = '$intakeid'"));
		$intakeformid = $intake['intakeformid'];
		if($intakeformid > 0) {
			$pdf_id = $intake['pdf_id'];

			$user_form_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT `user_form_id` FROM `intake_forms` WHERE `intakeformid` = '$intakeformid'"))['user_form_id'];

			include('../Intake/attach_services_sales.php');
		}

		$echo_script = '<script type="text/javascript"> parent.window.location.href = "'.WEBSITE_URL.'/Sales/sale.php?p=details&id='.$salesid.'"; </script>';
	}

	if($action == 'create' || $contact_update_info == 1 || $contactid == 'NEW_CONTACT') {
		if(!($contactid > 0)) {
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`) VALUES ('$src_table', '$category')");
			$contactid = mysqli_insert_id($dbc);
		}
		mysqli_query($dbc, "INSERT INTO `contacts_cost` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) num FROM `contacts_cost` WHERE `contactid` = '$contactid') rows WHERE num.rows = 0");
		mysqli_query($dbc, "INSERT INTO `contacts_dates` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) num FROM `contacts_dates` WHERE `contactid` = '$contactid') rows WHERE num.rows = 0");
		mysqli_query($dbc, "INSERT INTO `contacts_description` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) num FROM `contacts_description` WHERE `contactid` = '$contactid') rows WHERE num.rows = 0");
		mysqli_query($dbc, "INSERT INTO `contacts_medical` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) num FROM `contacts_medical` WHERE `contactid` = '$contactid') rows WHERE num.rows = 0");

		$intake = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake` WHERE `intakeid` = '$intakeid'"));
		if($intake_type == 'web') {
			$contact_name = explode(' ', $intake['name'], 2);
			$first_name = encryptIt($contact_name[0]);
			$last_name = encryptIt($contact_name[1]);
			$phone = encryptIt($intake['phone']);
			$email = encryptIt($intake['email']);

			mysqli_query($dbc, "UPDATE `contacts` SET `first_name` = '$first_name', `last_name` = '$last_name', `home_phone` = '$phone', `email_address` = '$email' WHERE `contactid` = '$contactid'");
		} else {
			$intakeformid = $intake['intakeformid'];
			$pdf_id = $intake['pdf_id'];

			$user_form_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT `user_form_id` FROM `intake_forms` WHERE `intakeformid` = '$intakeformid'"))['user_form_id'];
			$user_field_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT `intake_field` FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['intake_field'];
			$user_form_field = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `field_id` = '$user_field_id'"));
			$field_name = $user_form_field['name'];
			$form_fields = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$user_form_id' AND `name` = '$field_name' AND `type` = 'OPTION' AND `deleted` = 0"),MYSQLI_ASSOC);

			$update_arr = [];
			foreach($form_fields as $form_field) {
				$form_data = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$form_field['field_id']."'"));
				$update_arr[$form_field['source_conditions']] = $form_data['value'];
			}

			foreach($update_arr as $field => $value) {
				if (isEncrypted($field)) {
					$value = encryptIt($value);
				}
				mysqli_query($dbc, "UPDATE `contacts` LEFT JOIN `contacts_cost` ON `contacts`.`contactid`=`contacts_cost`.`contactid` LEFT JOIN `contacts_dates` ON `contacts`.`contactid`=`contacts_dates`.`contactid` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` LEFT JOIN `contacts_medical` ON `contacts`.`contactid`=`contacts_medical`.`contactid` SET `$field` = '$value' WHERE  `contacts`.`contactid`='$contactid'");
			}
		}
	}

	$before_change = capture_before_change($dbc, 'intake', 'contactid', 'intakeid', $intakeid);
	$before_change .= capture_before_change($dbc, 'intake', 'assigned_date', 'intakeid', $intakeid);

	mysqli_query($dbc, "UPDATE `intake` SET `contactid` = '$contactid', `assigned_date` = '$assigned_date' WHERE `intakeid` = '$intakeid'");

	$history = capture_after_change('contactid', $contactid);
	$history .= capture_after_change('assigned_date', $assigned_date);
	add_update_history($dbc, 'intake_history', $history, '', $before_change);

	if(!empty($_GET['from_salesid'])) {
		$salesid = $_GET['from_salesid'];
		$sales_contactid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid` FROM `sales` WHERE `salesid` = '$salesid'"))['contactid'];
		$sales_contactid .= ','.$contactid;
		$sales_contactid = trim($sales_contactid, ',');
		mysqli_query($dbc, "UPDATE `sales` SET `contactid` = '$sales_contactid' WHERE `salesid` = '$salesid'");
		$echo_script = '<script type="text/javascript"> parent.window.location.href = "'.WEBSITE_URL.'/Sales/sale.php?p=salespath&id='.$salesid.'"; </script>';
		$echo_script .= '<script type="text/javascript"> window.open("'.WEBSITE_URL.'/'.ucfirst($src_table).'/contacts_inbox.php?category='.$category.'&edit='.$contactid.'", "_blank"); </script>';
	} else if(!empty($_GET['from_projectid'])) {
		$projectid = $_GET['from_projectid'];
		$clientid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `clientid` FROM `project` WHERE `projectid` = '$projectid'"))['clientid'];
		$clientid .= ','.$contactid;
		$clientid = trim($clientid, ',');
		mysqli_query($dbc, "UPDATE `project` SET `clientid` = '$clientid' WHERE `projectid` = '$projectid'");
		$echo_script = '<script type="text/javascript"> parent.window.location.href = "'.WEBSITE_URL.'/Project/projects.php?edit='.$projectid.'"; </script>';
		$echo_script .= '<script type="text/javascript"> window.open("'.WEBSITE_URL.'/'.ucfirst($src_table).'/contacts_inbox.php?category='.$category.'&edit='.$contactid.'", "_blank"); </script>';
	} else if($action == 'create' || $action == 'assign') {
		$echo_script = '<script type="text/javascript"> parent.window.location.href = "'.WEBSITE_URL.'/'.ucfirst($src_table).'/contacts_inbox.php?category='.$category.'&edit='.$contactid.'"; </script>';
		if($new_injury == 'Injury') {
			$echo_script .= '<script type="text/javascript"> window.open("'.WEBSITE_URL.'/Injury/add_injury.php?contactid='.$contactid.'", "_blank"); </script>';
		}
	} else if($action == 'project') {
		if($contactid > 0) {
			$clientid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `clientid` FROM `project` WHERE `projectid` = '$projectid'"))['clientid'];
			$clientid .= ','.$contactid;
			$clientid = trim($clientid, ',');
			mysqli_query($dbc, "UPDATE `project` SET `clientid` = '$clientid' WHERE `projectid` = '$projectid'");
			$echo_script .= '<script type="text/javascript"> window.open("'.WEBSITE_URL.'/'.ucfirst($src_table).'/contacts_inbox.php?category='.$category.'&edit='.$contactid.'", "_blank"); </script>';
		}
	} else if($action == 'ticket') {
		if($contactid > 0) {
			$clientid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `clientid` FROM `tickets` WHERE `ticketid` = '$ticketid'"))['clientid'];
			if(!($clientid > 0)) {
				mysqli_query($dbc, "UPDATE `tickets` SET `clientid` = '$contactid' WHERE `ticketid` = '$ticketid'");
			}
			$echo_script .= '<script type="text/javascript"> window.open("'.WEBSITE_URL.'/'.ucfirst($src_table).'/contacts_inbox.php?category='.$category.'&edit='.$contactid.'", "_blank"); </script>';
		}
	} else if($action == 'sales') {
		if($contactid > 0) {
			$sales_contactid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid` FROM `sales` WHERE `salesid` = '$salesid'"))['contactid'];
			$sales_contactid .= ','.$contactid;
			$sales_contactid = trim($sales_contactid, ',');
			mysqli_query($dbc, "UPDATE `sales` SET `contactid` = '$sales_contactid' WHERE `salesid` = '$salesid'");
			$echo_script .= '<script type="text/javascript"> window.open("'.WEBSITE_URL.'/'.ucfirst($src_table).'/contacts_inbox.php?category='.$category.'&edit='.$contactid.'", "_blank"); </script>';
		}
	}

	include('../Intake/check_intake_services.php');
	if(!empty($intake_services) && $contactid > 0) {
		include('../Intake/check_customer_rate_card.php');
	}
	if(!empty($intake_services) && $projectid > 0) {
		include('../Intake/add_project_services.php');
	}

	echo $echo_script;
} else { ?>
	<script type="text/javascript">
		$(document).ready(function() {
			selectCategory();
			initSelectOnChange();
			$('[name="complete_form"]').click(function() {
				loadingOverlayShow('html');
			});
		});
		function selectCategory() {
			var category	= $('#contact_category').val().split('*#*')[1];
			var action		= $('#action').val();
			var intakeid	= $('#intakeid').val();

			if (action=='assign' || action=='project' || action == 'sales' || action == 'ticket') {
				$.ajax({
					type:		"GET",
					url:		"intake_ajax_all.php?fill=getContactsList&category="+category+"&action="+action,
					dataType:	"html",
					success:	function(response) {
						destroyInputs('.intake_block');
						$("#contact_list").html(response);
						$("#contact_list").trigger("change.select2");
						initInputs('.intake_block');
						initSelectOnChange();
					}
				});
			}
		}
		function selectProjectType(sel) {
			var projecttype = $(sel).val();
			$('select[name="projectid"]').find('option').hide();
			$('select[name="projectid"]').find('option[data-type="'+projecttype+'"]').show();
			$('select[name="projectid"]').find('option[value="NEW_PROJECT"]').show();
			$('select[name="projectid"]').trigger('change.select2');
		}
		function selectProject(sel) {
			var projectid = $(sel).val();
			var projecttype = $(sel).find('option:selected').data('type');
			if(projectid == 'NEW_PROJECT') {
				$('#project_name').show();
			} else {
                if(projectid > 0) {
                    $('#project_path').show().load('project_path.php?projectid='+projectid+'&intakeid=<?= $_GET['intakeid'] ?>');
                }
				$('#project_name').hide();
				$('select[name="projecttype"]').val(projecttype);
				$('select[name="projecttype"]').trigger('change.select2');
			}
		}
		function selectTicketType(sel) {
			var tickettype = $(sel).val();
			$('select[name="ticketid"]').find('option').hide();
			$('select[name="ticketid"]').find('option[data-type="'+tickettype+'"]').show();
			$('select[name="ticketid"]').find('option[value="NEW_TICKET"]').show();
			$('select[name="ticketid"]').trigger('change.select2');
		}
		function selectTicket(sel) {
			var ticketid = $(sel).val();
			var tickettype = $(sel).find('option:selected').data('type');
			if(ticketid == 'NEW_TICKET') {
				$('#ticket_name').show();
			} else {
				$('#ticket_name').hide();
				$('select[name="tickettype"]').val(tickettype);
				$('select[name="tickettype"]').trigger('change.select2');
			}
		}
		function selectSales(sel) {
			if(sel.value == 'NEW_SALES') {
				$('.new_sales').show();
			} else {
				$('.new_sales').hide();
			}
		}
		function initSelectOnChange() {
			$('select[name="contact_category"]').on('change', function() { selectCategory(this); });
			$('select[name="projecttype"]').on('change', function() { selectProjectType(this); });
			$('select[name="projectid"]').on('change', function() { selectProject(this); });
			$('select[name="ticket_type"]').on('change', function() { selectTicketType(this); });
			$('select[name="ticketid"]').on('change', function() { selectTicket(this); });
			$('select[name="salesid"]').on('change', function() { selectSales(this); });
		}
	</script>
</head>

<body>
    <div class="loading_overlay" style="display: none;"><div class="loading_wheel"></div></div>
	<div class="container intake_block">
		<div class="row"><?php
			$subtitle	  = $_GET['subtitle'];
			$action		  = $_GET['action'];
			$intakeid	  = $_GET['intakeid'];
            $contact_type = $_GET['contact_type'];
            $src_table    = $_GET['src_table'];
            if(empty($contact_type)) {
            	$contact_type = '';
            }
            if(empty($src_table)) {
            	$src_table = 'contacts';
            } ?>

			<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
				<input type="hidden" name="action" id="action" value="<?= $action; ?>" />
				<input type="hidden" name="intakeid" id="intakeid" value="<?= $intakeid; ?>" />
				<input type="hidden" name="from_projectid" id="from_projectid" value="<?= $_GET['from_projectid'] ?>">
				<input type="hidden" name="intake_type" id="intake_type" value="<?= $_GET['intake_type'] ?>">
				<input type="hidden" name="new_injury" id="new_injury" value="<?= $_GET['new_injury'] ?>"><?php
				echo '<h1>' . $subtitle . '</h1>';
				if($action == 'project' || $action == 'sales' || $action == 'ticket') { ?>
		            <div class="notice gap-bottom gap-top popover-examples">
		                <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
		                <div class="col-sm-11"><span class="notice-name">NOTE:</span>
			            Leave Contact dropdown empty if you do not wish to assign this to a Contact. Submitting this form will redirect this page to the <?= PROJECT_NOUN ?> and open a new tab for the Contact.</div>
		                <div class="clearfix"></div>
		            </div><?php
		        }

				if($action == 'project' || $action == 'sales' || $action == 'ticket') {
					echo '<p class="gap-left"></p>';
				}

                $cat_text = 'Please select a Contact category:';
                echo '<p class="gap-left">'. $cat_text .'</p>';

                if(tile_enabled($dbc, 'contacts') || tile_enabled($dbc, 'contacts_inbox')) {
					$all_tiles['contacts'] = array_unique(array_filter(explode(',', get_config($dbc, 'contacts_tabs'))));
                }
                if(tile_enabled($dbc, 'contacts_rolodex')) {
					$all_tiles['contactsrolodex'] = array_unique(array_filter(explode(',', get_config($dbc, 'contactsrolodex_tabs'))));
                }
                if(tile_enabled($dbc, 'contacts3')) {
					$all_tiles['contacts3'] = array_unique(array_filter(explode(',', get_config($dbc, 'contacts3_tabs'))));
				}
                if(tile_enabled($dbc, 'client_info')) {
					$all_tiles['clientinfo'] = array_unique(array_filter(explode(',', get_config($dbc, 'clientinfo_tabs'))));
				}
                if(tile_enabled($dbc, 'members')) {
					$all_tiles['members'] = array_unique(array_filter(explode(',', get_config($dbc, 'members_tabs'))));
				}
                if(tile_enabled($dbc, 'vendors')) {
					$all_tiles['vendors'] = array_unique(array_filter(explode(',', get_config($dbc, 'vendors_tabs'))));
				}
				?>

				<div class="gap-left">
                    <select name="contact_category" id="contact_category" data-placeholder="Select a Contact category" width="380" class="chosen-select-deselect form-control">
                        <option value=""></option><?php
						foreach($all_tiles as $tile_name => $tile_cats) {
							foreach ($tile_cats as $cat_name) {
								$selected_cat = '';
								if($src_table == $tile_name && $contact_type == $cat_name) {
									$selected_cat = 'selected';
								}
								echo '<option value="'.$tile_name.'*#*'.$cat_name.'" '.$selected_cat.'>'.$cat_name.'</option>';
							}
						} ?>
                    </select>

					<?php /* Select the Contact */
					if ( $action == 'assign' || $action == 'project' || $action == 'sales' || $action == 'ticket' ) { ?>
						<br /><br />
						<p>Select the Contact you want this Intake Form Submission to assign to:</p>
						<select name="contact_list" id="contact_list" data-placeholder="Select a Contact" width="380" class="chosen-select-deselect form-control">
						</select>
						<br /><br />
						<p><label class="form-checkbox" style="max-width: none;"><input type="checkbox" name="contact_update_info" value="1"> Update Contact Information With Form Details</label></p><?php
					} ?>

				</div><?php
				if ( $action=='project') { ?>
					<br>
					<div class="gap-left">
						<p>Please select a <?= PROJECT_NOUN ?> Type:</p>
						<select name="projecttype" id="projecttype" data-placheolder="Select a <?= PROJECT_NOUN ?> Type" width="380" class="chosen-select-deselect form-control">
							<option value=""></option><?php
							$project_tabs = get_config($dbc, 'project_tabs');
							$project_tabs = explode(',',$project_tabs);
							foreach($project_tabs as $item) {
								$var_name = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
								if($var_name == 'client' || check_subtab_persmission($dbc, 'project', ROLE, $var_name) == 1) {
									echo "<option value='$var_name'>$item</option>";
								}
							} ?>
						</select>

						<br><br>
						<p>Select a <?= PROJECT_NOUN ?> you want this Intake Form Submission to assign to:</p>
						<select name="projectid" id="projectid" data-placeholder="Select a <?= PROJECT_NOUN ?>" width="380" class="chosen-select-deselect form-control">
							<option value=""></option>
							<option value="NEW_PROJECT">Create New <?= PROJECT_NOUN ?></option><?php
							$project_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `project` WHERE `deleted` = 0"),MYSQLI_ASSOC);
							foreach ($project_list as $project) {
								echo "<option data-type='".$project['projecttype']."' value='".$project['projectid']."'>".get_project_label($dbc, $project)."</option>";
							}
							?>
						</select>

						<div id="project_path" style="<?= $_GET['from_projectid'] > 0 ? '' : 'display: none;' ?>">
                            <?php $projectid = $_GET['from_projectid'];
                            include('project_path.php'); ?>
                        </div>
						<div id="project_name" style="display: none;">
							<br>
							<p>Select a Name for this new <?= PROJECT_NOUN ?>:</p>
							<input type="text" name="project_name" class="form-control">
						</div>
					</div>
				<?php }
				if ( $action=='ticket') { ?>
					<br>
					<div class="gap-left">
						<?php $ticket_tabs = array_filter(explode(',',get_config($dbc, 'ticket_tabs')));
						if(count($ticket_tabs) > 0) { ?>
							<p>Please select a <?= TICKET_NOUN ?> Type:</p>
							<select name="ticket_type" id="ticket_type" data-placheolder="Select a <?= TICKET_NOUN ?> Type" width="380" class="chosen-select-deselect form-control">
								<option value=""></option><?php
								$ticket_type = get_config($dbc, 'default_ticket_type');
								foreach($ticket_tabs as $item) {
									$var_name = config_safe_str($item);
									if(check_subtab_persmission($dbc, 'ticket', ROLE, 'ticket_type_'.$var_name) == 1) {
										echo "<option ".($ticket_type == $var_name ? 'selected' : '')." value='$var_name'>$item</option>";
									}
								} ?>
							</select>
						<?php } ?>

						<br><br>
						<p>Select a <?= TICKET_NOUN ?> you want this Intake Form Submission to assign to:</p>
						<select name="ticketid" id="ticketid" data-placeholder="Select a <?= TICKET_NOUN ?>" width="380" class="chosen-select-deselect form-control">
							<option value=""></option>
							<option value="NEW_TICKET">Create New <?= TICKET_NOUN ?></option><?php
							$ticket_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `deleted` = 0"),MYSQLI_ASSOC);
							foreach ($ticket_list as $ticket) {
								echo "<option ".(!empty($ticket_type) && $ticket_type != $ticket['ticket_type'] ? 'style="display:none;"' : '')." data-type='".$ticket['ticket_type']."' value='".$ticket['ticketid']."'>".get_ticket_label($dbc, $ticket)."</option>";
							}
							?>
						</select>

						<div id="ticket_name" style="display: none;">
							<br>
							<p>Select a Name for this new <?= TICKET_NOUN ?>:</p>
							<input type="text" name="ticket_name" class="form-control">
						</div>
					</div>
				<?php }
				if ( $action=='sales') { ?>
					<br>
					<div class="gap-left">
						<p>Select a <?= SALES_NOUN ?> you want this Intake Form Submission to assign to:</p>
						<select name="salesid" id="salesid" data-placeholder="Select a <?= SALES_NOUN ?>" width="380" class="chosen-select-deselect form-control">
							<option value=""></option>
							<option value="NEW_SALES">Create New <?= SALES_NOUN ?></option><?php
							$sales_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `sales` WHERE `deleted` = 0"),MYSQLI_ASSOC);
							foreach ($sales_list as $sales) {
								$sales_label = "Sales #".$sales['salesid'];
								if(!empty($sales['businessid'])) {
									$sales_label .= ": ".get_client($dbc, $sales['businessid']);
								}
								$sales_contacts = [];
								if(!empty($sales['contactid'])) {
									foreach (explode(',', $sales['contactid']) as $sales_contact) {
										if($sales_contact > 0) {
											$sales_contacts[] = !empty(get_client($dbc, $sales_contact)) ? get_client($dbc, $sales_contact) : get_contact($dbc, $sales_contact);
										}
									}
								}
								$sales_contacts = implode(', ', $sales_contacts);
								if(!empty($sales_contacts)) {
									$sales_label .= ': '.$sales_contacts;
								}
								echo "<option value='".$sales['salesid']."'>".$sales_label."</option>";
							}
							?>
						</select>
					</div>
					<?php $limit_staff_cat = array_filter(explode(',',get_config($dbc, 'sales_limit_staff_cat')));
					$cat_query = '';
					if(!empty($limit_staff_cat)) {
					    $cat_query = [];
					    foreach($limit_staff_cat as $staff_cat) {
					        $cat_query[] = "CONCAT(',',`staff_category`,',') LIKE ('%,".$staff_cat.",%')";
					    }
					    $cat_query = " AND (".implode(' OR ', $cat_query).")";
					}
					$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted` = 0 AND `category` iN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status` > 0".$cat_query)); ?>
					<br>
					<div class="gap-left new_sales" style="display:none;">
						<p>Primary Staff:</p>
						<select name="sales_primary_staff" data-placeholder="Select a Staff" class="chosen-select-deselect form-control">
							<option></option>
							<?php foreach($staff_list as $staff) {
								echo '<option value="'.$staff['contactid'].'">'.$staff['full_name'].'</option>';
							} ?>
						</select>
					</div>
					<br>
					<div class="gap-left new_sales" style="display:none;">
						<p>Share Lead:</p>
						<select name="sales_share_lead[]" multiple="" data-placeholder="Select a Staff" class="chosen-select-deselect form-control">
							<?php foreach($staff_list as $staff) {
								echo '<option value="'.$staff['contactid'].'">'.$staff['full_name'].'</option>';
							} ?>
						</select>
					</div>
				<?php } ?>
				<div class="gap-top">
			        <button class="btn brand-btn pull-right" name="complete_form" value="complete_form">Submit</button>
			    </div>
			</form>
		</div><!-- .row -->
	</div><!-- .container -->

	<?php include ('../footer.php');
} ?>
