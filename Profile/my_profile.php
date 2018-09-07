<?php if(!isset($_GET['mobile_view'])) {
	include_once ('../include.php');
} else {
	include_once ('../database_connection.php');
	include_once ('../global.php');
	include_once ('../function.php');
	include_once ('../output_functions.php');
	include_once ('../email.php');
	include_once ('../user_font_settings.php');
}
$rookconnect = get_software_name();
error_reporting(0);
$_GET['contactid'] = $_SESSION['contactid']; ?>

</head>
<script type="text/javascript" src="profile.js"></script>
<?php include_once ('edit_contact_access.php') ?>
<body>
<?php if(!isset($_GET['mobile_view'])) { include_once ('../navigation.php'); }
include('../Contacts/contact_field_arrays.php');

if (isset($_POST['contactid'])) {
	$_GET['contactid'] = $_SESSION['contactid'];
	$category = 'Staff';
	if($_GET['edit_contact'] == 'true' && !isset($_POST['overview_page'])) {
		include('../Contacts/contacts_save.php');
		if (isset($_POST['edit_software_access'])) {
			include('../Profile/contacts_save_software_access.php');
		}
	} ?>
	<script>
		if(window.self === window.top) {
			<?php if(empty($_POST['subtab'])) {
				echo 'window.location.replace("my_profile.php?edit_contact='.$_GET['edit_contact'].'");';
			} else {
				if($_POST['subtab'] == 'certificates') {
					echo 'window.location.replace("my_certificate.php?edit_contact='.$_GET['edit_contact'].'");';
				} else if($_POST['subtab'] == 'goals') {
					echo 'window.location.replace("gao_goal.php?edit_contact='.$_GET['edit_contact'].'");';
				} else if($_POST['subtab'] == 'daysheet') {
					echo 'window.location.replace("daysheet.php");';
				} else if($_POST['subtab'] == 'schedule') {
					echo 'window.location.replace("staff_schedule.php");';
				}
			} ?>
		}
		else if('<?php echo $category; ?>' == 'Business') {
			$('[name=new_business]', top.document).val('<?php echo $contactid; ?>');
		}
		else {
			var contacts = $('[name=related-contacts]', top.document).val();
			if(contacts != '')
				contacts = JSON.parse(contacts);
			else
				contacts = new Array();
			contacts.push('<?php echo $contactid; ?>');
			$('[name=related-contacts]', top.document).val(JSON.stringify(contacts));
		}
	</script>
	<?php
}
include('../Contacts/contacts_fields.php');
$subtab = isset($_POST['subtab']) ? $_POST['subtab'] : 'id_card';
$field_tabs = ','.get_config($dbc, 'staff_field_subtabs').',';
if(strpos($field_tabs,',Software ID,' === FALSE)) {
	$field_tabs .= 'Software ID,';
}
if($_GET['from_staff_tile'] == 'true') {
	$_GET['from_staff_tile'] = false;
	foreach(explode(',',$field_tabs) as $field_tab) {
		if($field_tab != 'id_card') {
			$subtab = $field_tab;
			break;
		}
	}
}
?>
<div class="container">
	<?php if(!isset($_GET['mobile_view'])) {
		include('mobile_view.php'); ?>
		<div class="row hide-titles-mob">
			<!-- <div id="no-more-tables" class="main-screen contacts-list"> -->
			<div class="main-screen">
				<!-- Tile Header -->
	            <div class="tile-header standard-header">
					<div class="col-xs-12 col-sm-4">
						<h1>
							<span class="pull-left" style="margin-top: -5px;"><a href="my_profile.php" class="default-color">My Profile</a></span>
							<span class="clearfix"></span>
						</h1>
					</div>
					<div class="col-xs-12 col-sm-8 text-right settings-block">
						<form action="?<?= $_GET['edit_contact'] != 'true' ? 'edit_contact=true' : '' ?>" method="post" id="edit_contact">
							<?php if ($subtab == 'id_card') {
								$edit_subtab = "$('[name=subtab]').val('staff_information');";
							} else {
								$edit_subtab = '';
							} ?>
							<button name="subtab" value="<?= $subtab ?>" onclick="<?= $edit_subtab ?>$('#edit_contact').submit();" class="btn brand-btn pull-right"><?= $_GET['edit_contact'] != 'true' ? 'Edit' : 'View' ?></button>
						</form>
						<a href="<?= WEBSITE_URL ?>/Daysheet/daysheet.php" class="btn brand-btn pull-right">Planner</a>
					</div>
					<div class="clearfix"></div>
				</div><!-- .tile-header -->

				<form id="form1" name="form1" method="post" action="my_profile.php?edit_contact=<?= $_GET['edit_contact'] ?>" enctype="multipart/form-data" class="form-horizontal" role="form">

					<!-- Sidebar -->
					<div class="standard-collapsible tile-sidebar set-section-height">
						<?php include('tile_sidebar.php'); ?>
					</div><!-- .tile-sidebar -->

					<!-- Main Screen -->
	                <div class="has-main-screen scale-to-fill tile-content set-section-height" style="padding: 0; overflow-y: auto;">
						<div class="main-screen-details main-screen override-main-screen <?= $subtab != 'id_card' ? 'standard-body' : '' ?>" style="height: inherit;">
							<?php if($subtab != 'id_card') { ?>
								<div class='standard-body-title'>
									<h3><?= $sidebar_fields[$subtab][1]; ?></h3>
								</div>
							<?php } ?>
							<div class='standard-body-dashboard-content pad-top pad-left pad-right'>
								<?php if($subtab == 'id_card') {
									include('../Contacts/contact_profile.php');
									echo '<input type="hidden" name="overview_page" value="1">';
								} else {
									$staff_cat_query = [];
									if(!empty($staff_category)) {
										foreach(array_filter(explode(',', $staff_category)) as $staff_cat) {
											$staff_cat_query[] = " `tab`='Staff_".config_safe_str($staff_cat)."'";
										}
									}
									if(!empty($staff_cat_query)) {
										$staff_cat_query = " OR ".implode(" OR ", $staff_cat_query);
									} else {
										$staff_cat_query = "";
									}
									$query_main = mysqli_query($dbc,"SELECT accordion, subtab, tab FROM field_config_contacts `main_table` WHERE (tab='Staff' ".$staff_cat_query.") AND `accordion` IS NOT NULL AND (`tab` = 'Staff' OR `accordion` NOT IN (SELECT `accordion` FROM `field_config_contacts` `other_table` WHERE `tab` = 'Staff' AND IFNULL(`accordion`,'') != '' AND `main_table`.`subtab` = `other_table`.`subtab`)) ORDER BY IFNULL(`subtab`,'') = '$subtab', IFNULL(`order`,`configcontactid`)");
									$field_exists = [];

									$j=0;
									while($row_main = mysqli_fetch_array($query_main)) {
										$accordion = $row_main['accordion'];
										$this_tab = $row_main['subtab'];
										$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contacts FROM field_config_contacts WHERE tab='".$row_main['tab']."' AND subtab='$this_tab' AND accordion='$accordion'"));

										$value_config = explode(',',$get_field_config['contacts']);
										if(!empty($staff_category) && $row_main['tab'] == 'Staff') {
											foreach(array_filter(explode(',', $staff_category)) as $staff_cat) {
												$cat_value_config = explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contacts FROM field_config_contacts WHERE tab='Staff_".config_safe_str($staff_cat)."' AND subtab='$this_tab' AND accordion='$accordion'"))['contacts']);
												$value_config = array_merge($value_config, $cat_value_config);
											}
										}

										foreach($value_config as $value_i => $value_field) {
											if(in_array($value_field, $field_exists)) {
												unset($value_config[$value_i]);
											}
										}
										$field_exists = array_merge($field_exists,$value_config);

										$value_config = ','.implode(',',$value_config).',';
										$value_config = str_replace(',Role,',',',$value_config);
										$value_config = str_replace(',User Name,',',',$value_config);
										$value_config = str_replace(',Password,',',',$value_config);
										if(str_replace(',','',$value_config) != '') {
											$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT contacts FROM field_config_contacts WHERE tab='Profile' AND subtab='$this_tab' AND accordion='$accordion'"));
											$edit_config = ','.$get_field_config['contacts'].',';
											?>

											<div <?php echo ($row_main['subtab'] == $subtab ? '' : 'style="display:none;"'); ?>>
												<h4><?= $row_main['accordion'] ?></h4>

												<div>
													<?php
														include ('../Contacts/add_contacts_basic_info.php');
														include ('../Contacts/add_contacts_dates.php');
														include ('../Contacts/add_contacts_cost.php');
														include ('../Contacts/add_contacts_description.php');
														include ('../Contacts/add_contacts_upload.php');
													?>
												</div>
											</div>
										<?php }
										$j++;
									}
								}
								if($subtab == 'software_access') {
									include('../Profile/my_software_access.php');
								} else if($subtab == 'time_off') {
									$tab = '%';
									$form = 'Time Off Request'; ?>
									<style>
									.external-form-submit .panel .panel-title a {
										color: #FFF !important;
									}
									</style>
									<div class="external-form-submit">
										<input type="hidden" name="subtab" value="time_off_requests">
										<?php include('../HR/time_off_request/time_off_request.php'); ?>
										<button class="btn brand-btn pull-right" type="submit" name="time_off_request" value="time_off">Submit Request</button>
										<div class="clearfix"></div>
									</div>
								<?php } else if($subtab == 'time_off_requests') {
									if($_POST['time_off_request'] == 'time_off') {
										$hrid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `hrid` FROM `hr` WHERE `form`='Time Off Request'"))['hrid'];
										$url_redirect = 'N/A';
										$tab = 'Form';
										include('../HR/time_off_request/save_time_off_request.php');
									}
									$requests = mysqli_query($dbc, "SELECT * FROM `hr_time_off_request` WHERE `contactid`='$contactid' ORDER BY `today_date`");
									if(mysqli_num_rows($requests) > 0) { ?>
										<div id="no-more-tables">
											<h3>Time Off Requests</h3>
											<table class="table table-bordered">
												<tr class="hide-titles-mob">
													<th>Type of Time Off</th>
													<th>Date</th>
													<th>Reason</th>
													<th>Signed</th>
													<th>Form</th>
												</tr>
												<?php while($request = mysqli_fetch_assoc($requests)) {
													$fields = explode('**FFM**',$request['fields']); ?>
													<tr>
														<td data-title="Type of Time Off"><?= $fields[0] == 'Other' ? $fields[1] : $fields[0] ?></td>
														<td data-title="Dates"><?= $fields[2].' - '.$fields[3] ?></td>
														<td data-title="Reason"><?= html_entity_decode($request['desc']) ?></td>
														<td data-title="Signed"><?= get_contact($dbc, $request['contactid']) ?><br /><?= $request['today_date'] ?></td>
														<td data-title=""><a href="../HR/time_off_request/download/hr_<?= $request['fieldlevelriskid'] ?>.pdf">View Request</a><br />
															Status: <?= $request['status'] ?></td>
													</tr>
												<?php } ?>
											</table>
										</div>
									<?php } else {
										echo "<h3>No Requests Found.</h3>";
									}
								}
								?>
							<?php if($subtab != 'id_card') { ?>
								<button type='submit' name='contactid' value='<?php echo $contactid; ?>' class="btn brand-btn pull-right">Submit</button>
								<a href='<?php echo WEBSITE_URL; ?>/home.php' class="btn brand-btn pull-right">Back</a>
							<?php } ?>
							</div>
						</div>
						<div class="clearfix"></div>
						
					</form>
				</div>
			</div>
		</div>
	<?php } else if($subtab == 'id_card') {
		include('../Contacts/contact_profile.php');
	} else {
		if($subtab != 'id_card') { ?>
			<form id="form1" name="form1" method="post" action="my_profile.php?edit_contact=<?= $_GET['edit_contact'] ?>" enctype="multipart/form-data" class="form-horizontal" role="form">
				<div class="main-screen-details">
		<?php }
		$query_main = mysqli_query($dbc,"SELECT accordion, subtab FROM field_config_contacts WHERE tab='Staff' AND `accordion` IS NOT NULL AND `order` IS NOT NULL ORDER BY IFNULL(`subtab`,'') = '$subtab', `order`");

		$j=0;
		while($row_main = mysqli_fetch_array($query_main)) {
			$accordion = $row_main['accordion'];
			$this_tab = $row_main['subtab'];
			$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contacts FROM field_config_contacts WHERE tab='Staff' AND subtab='$this_tab' AND accordion='$accordion'"));

			$value_config = ','.$get_field_config['contacts'].',';
			$value_config = str_replace(',Role,',',',$value_config);
			$value_config = str_replace(',User Name,',',',$value_config);
			$value_config = str_replace(',Password,',',',$value_config);
			if(str_replace(',','',$value_config) != '') {
				$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT contacts FROM field_config_contacts WHERE tab='Profile' AND subtab='$this_tab' AND accordion='$accordion'"));
				$edit_config = ','.$get_field_config['contacts'].',';
				?>

				<div <?php echo ($row_main['subtab'] == $subtab ? '' : 'style="display:none;"'); ?>>
					<h4><?= $row_main['accordion'] ?></h4>

					<div>
						<?php
							include ('../Contacts/add_contacts_basic_info.php');
							include ('../Contacts/add_contacts_dates.php');
							include ('../Contacts/add_contacts_cost.php');
							include ('../Contacts/add_contacts_description.php');
							include ('../Contacts/add_contacts_upload.php');
						?>
					</div>
				</div>
			<?php }
			$j++;
		}
		if($subtab == 'software_id') {
			include('../Profile/my_software_access.php');
		}
		if($subtab != 'id_card') { ?>
				<button type='submit' name='contactid' value='<?php echo $contactid; ?>' class="btn brand-btn pull-right">Submit</button>
				</div>
			</form>
		<?php }
	} ?>
</div>
<?php include ('../footer.php'); ?>