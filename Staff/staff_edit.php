<?php error_reporting(0);
// if(!isset($_GET['mobile_view'])) {
	include_once ('../include.php');
// } else {
// 	include_once ('../database_connection.php');
// 	include_once ('../global.php');
// 	include_once ('../function.php');
// 	include_once ('../output_functions.php');
// 	include_once ('../email.php');
// 	include_once ('../user_font_settings.php');
// }
checkAuthorised('staff'); 
$rookconnect = get_software_name(); ?>
<script type="text/javascript">
	/*$(window).scroll(scrollScreen);
    function scrollScreen() {
    	var cutoff = $(window).scrollTop();
        $('div h4').each(function(){
        	$(".sidebar a li").removeClass('active blue');
            if($(this).offset().top + $(this).height() > cutoff){
            	var tab_title = $("div h4:visible").html();
            	$(".sidebar a li").each(function(){
    	        	if($(this).html() == tab_title){
    	        		$(this).addClass('active blue');
    	        		$(window).scrollTop($(this).offset().top);
    	        	}
            	});
            }
        });
    }*/

    $(document).ready(function () {

    	$("#form1").find('input').addClass('saveonajax');
    	$("#form1").find('select').addClass('saveonajax');
    	$("#form1").find('textarea').addClass('saveonajax');
        	
    	$('input,select,textarea,.select2').each(function() {
    		if($(this).prop('readonly') == true) {
    			$(this).closest('div.form-group').addClass('viewonly_fields');
    		}
    	});
		$('.viewonly_fields').each(function() { viewOnlyFields(this); });
        $("#form1").submit(function( event ) {
            var category = $("#category").val();
            var sub_category = $("#sub_category").val();

            var code = $("input[name=code]").val();
            var name = $("input[name=name]").val();
            var category_name = $("input[name=category_name]").val();
            var sub_category_name = $("input[name=sub_category_name]").val();

            if (code == '' || category == '' || sub_category == '' || name == '') {
                //alert("Please make sure you have filled in all of the required fields.");
                //return false;
            }
            if(((category == 'Other') && (category_name == '')) || ((sub_category == 'Other') && (sub_category_name == ''))) {
                //alert("Please make sure you have filled in all of the required fields.");
                //return false;
            }
        });

        $("#self_identification").change(function() {
            if($("#self_identification option:selected").text() == 'New Self Identification') {
                    $( "#new_self_identification" ).show();
            } else {
                $( "#new_self_identification" ).hide();
            }
        });

		if(window.self != window.top) {
			var block = $('[name=businessid]').parent();
			block.empty();
			block.html("N/A");
		}
    });
	function viewOnlyFields(div) {
		$(div).find('input,select,textarea,.select2,img,a,button,.sigPad,.mce-container').each(function() {
			$(this).prop('readonly', true);
			if($(this).get(0).tagName.toLowerCase() == 'textarea') {
				$(this).closest('.col-sm-8').css('pointer-events', 'none');
				$(this).closest('.col-sm-8').css('opacity', '0.5');
			} else {
				$(this).css('pointer-events', 'none');
				$(this).css('opacity', '0.5');
			}
		});
	}

	$(document).on('change', '.saveonajax', function(){
	    addStaffData();
	});
	
	/*$(".saveonajax").blur(function(){
		addStaffData();
	});*/
	function addStaffData() {
		var formData = new FormData($("#form1")[0]);
		$.ajax({
			type: 'POST',
			url: '../Staff/staff_ajax.php?action=add_update_staff',
			//data: $('#form1').serialize(),
			data:formData,
			cache:false,
			processData: false,
	        contentType: false,
			success: function(response) {
				if(response!=''){
					$('#contact_id_val').val(response);
				}
			}
		});
	}
</script>
</head>
<script type="text/javascript" src="../Staff/staff.js"></script>
<body>
<?php if(!isset($_GET['mobile_view']) && $_GET['view_only'] != 'id_card') { include_once ('../navigation.php'); }
include('../Contacts/contact_field_arrays.php');

if (isset($_POST['contactid'])) {
	$category = 'Staff';
	if (!isset($_POST['overview_page'])) {
		include('../Contacts/contacts_save.php');
		if (isset($_POST['edit_software_access'])) {
			include('../Staff/contacts_save_software_access.php');
		}
	}
	?>
	<script>
	if(window.self === window.top) {
		<?php if(!empty($_POST['subtab'])) {
			if($_POST['subtab'] == 'certificates') {
				echo 'window.location.replace("certificate.php?contactid='.$_GET['contactid'].'");';
			} else if($_POST['subtab'] == 'history') {
				echo 'window.location.replace("staff_history.php?contactid='.$_GET['contactid'].'");';
			} else if($_POST['subtab'] == 'reminders') {
				echo 'window.location.replace("staff_reminder.php?contactid='.$_GET['contactid'].'");';
			} else if($_POST['subtab'] == 'schedule') {
				echo 'window.location.replace("staff_schedule.php?contactid='.$_GET['contactid'].'");';
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
else if(isset($_GET['status'])) {
    $date_of_archival = date('Y-m-d');
	$id = intval($_GET['contactid']);
	switch($_GET['status']) {
		case 'suspend': $status = "status=0, deleted=0"; $action = "suspended"; break;
		case 'activate': $status = "status=1, deleted=0"; $action = "activated"; break;
		case 'archive': $status = "status=0, deleted=1, `date_of_archival` = '$date_of_archival' "; $action = "archived"; break;
	}
	$sql = "update contacts set $status where contactid='$id'";
	$result = mysqli_query($dbc, $sql);
	echo "<script>window.location='staff.php?tab=active';</script>";
}
else if(isset($_GET['favourite'])) {
	$favcontactid = $_GET['favourite'];
	$query = "UPDATE contacts set is_favourite=REPLACE(CONCAT(IFNULL(`is_favourite`,''),',".$_SESSION['contactid'].",'),',,',',') where contactid = $favcontactid";
	$make_favourite = mysqli_query($dbc,$query);
	echo "<script>window.location='staff.php?tab=active';</script>";
}
else if(isset($_GET['unfavourite'])) {
	$unfavcontactid = $_GET['unfavourite'];
	$query = "UPDATE contacts set is_favourite=REPLACE(IFNULL(`is_favourite`,''),',".$_SESSION['contactid'].",',',') where contactid = $unfavcontactid";
	$make_favourite = mysqli_query($dbc,$query);
	echo "<script>window.location='staff.php?tab=active';</script>";
}
else if(!empty($_GET['subtab'])) {
	$action_page = 'staff_edit.php?contactid='.$_GET['contactid'];
	if($_GET['subtab'] == 'certificates') {
		$action_page = 'certificate.php?contactid='.$_GET['contactid'];
	} else if($_GET['subtab'] == 'ratecard') {
		$action_page = 'edit_staff_rate_card.php?contactid='.$_GET['contactid'];
	} else if($_GET['subtab'] == 'history') {
		$action_page = 'staff_history.php?contactid='.$_GET['contactid'];
	} else if($_GET['subtab'] == 'reminders') {
		$action_page = 'staff_reminder.php?contactid='.$_GET['contactid'];
	} else if($_GET['subtab'] == 'schedule') {
        $action_page = 'staff_schedule.php?contactid='.$_GET['contactid'];
    }?>
	<form action="<?php echo $action_page; ?>" method="post" id="change_page">
		<input type="hidden" name="subtab" value="<?php echo $_GET['subtab']; ?>">
	</form>
	<script type="text/javascript"> document.getElementById('change_page').submit(); </script>
<?php }

include('../Contacts/contacts_fields.php');
if(strpos(','.ROLE.',',',super,') === false) {
	$security_sql = "SELECT COUNT(*) numrows FROM `subtab_config` WHERE `tile`='staff' AND `subtab`='software_access' AND ',".trim(ROLE,',').",' LIKE CONCAT('%,',`security_level`,',%') ORDER BY IF(`status` like '%turn_off%', 0, 1)";
	$security_result = mysqli_fetch_array(mysqli_query($dbc, $security_sql));
	if($security_result['numrows'] == 0) {
		mysqli_query($dbc, "INSERT INTO `subtab_config` (`tile`, `subtab`, `security_level`, `status`) VALUES ('staff', 'software_access', '".trim(ROLE,',')."', '*turn_off*')");
	}
}
$field_tabs = ','.get_config($dbc, 'staff_field_subtabs').',';
$from_url = 'staff.php?tab=active';
if (!empty($_GET['from'])) {
	$from_url = $_GET['from'];
}
if(!empty($_GET['from_url'])) {
	$from_url = $_GET['from_url'];
}
if(!empty($_POST['from_url'])) {
	$from_url = $_POST['from_url'];
}

$security_levels = explode(',',trim(ROLE,','));
$subtabs_hidden = [];
$subtabs_viewonly = [];
$fields_hidden = [];
$fields_viewonly = [];
$i = 0; $mobile_mode = '0';
foreach($security_levels as $security_level) {
	$security_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_staff_security` WHERE `security_level` = '$security_level'"));
	if(!empty($security_config)) {
		if($i == 0) {
			$subtabs_hidden = explode(',',$security_config['subtabs_hidden']);
			$subtabs_viewonly = explode(',',$security_config['subtabs_viewonly']);
			$fields_hidden = explode(',',$security_config['fields_hidden']);
			$fields_viewonly = explode(',',$security_config['fields_viewonly']);
		} else {
			$subtabs_hidden = array_intersect(explode(',',$security_config['subtabs_hidden']), $subtabs_hidden);
			$subtabs_viewonly = array_intersect(explode(',',$security_config['subtabs_viewonly']), $subtabs_viewonly);
			$fields_hidden = array_intersect(explode(',',$security_config['fields_hidden']), $fields_hidden);
			$fields_viewonly = array_intersect(explode(',',$security_config['fields_viewonly']), $fields_viewonly);
		}
		$i++;
	}
} ?>
<div id="staff_div" class="container">
	<?php if($_GET['view_only'] == 'id_card') { ?>
		<div class="row" style="width: 100%;">
			<div class="main-screen" style="margin-top:0;">
				<div class="has-main-screen scale-to-fill tile-content set-section-height" style="height: auto;">
					<div class="main-screen-details main-screen override-main-screen <?= $subtab != 'id_card' ? 'standard-body' : '' ?>" style="height: inherit;">
						<div class='standard-body-dashboard-content pad-top pad-left pad-right'>
							<?php include('../Contacts/contact_profile.php'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } else if(!isset($_GET['mobile_view'])) { include('mobile_view.php'); }else{ $mobile_mode = '1';} ?>
	<div class="row hide-titles-mob" style="<?= $_GET['view_only'] == 'id_card' ? 'display:none;' : '' ?>">
		<!-- <div id="no-more-tables" class="main-screen contacts-list"> -->
		<div class="main-screen">
            <!-- Tile Header -->
            <div class="tile-header standard-header">
                <div class="col-sm-12">
                    <h1>
                        <span class="pull-left" style="margin-top: -5px;"><a href="<?= $from_url ?>" class="default-color">Staff</a>: <?= $contactid > 0 ? get_contact($dbc, $contactid) : 'Add New' ?></span>
						<?php if ( config_visible_function ( $dbc, 'staff' ) == 1 ) { ?>
							<div class="pull-right gap-left top-settings">
								<a href="staff.php?settings=dashboard" class="mobile-block pull-right "><img title="Tile Settings" src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>
								<span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							</div><?php
						} ?>
						<?php if(vuaed_visible_function($dbc, 'staff') > 0) { ?>
							<a href="staff_edit.php" class="btn brand-btn pull-right">New Staff</a>
						<?php } ?>
                        <span class="clearfix"></span>
						<div class="alert alert-danger text-sm text-center" style="display:none;"></div>
						<div class="alert alert-success text-sm text-center" style="display:none;"></div>
                    </h1>
                </div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

			<form id="form1" name="form1" method="post"	action="staff_edit.php?contactid=<?= $_GET['contactid'] ?>" enctype="multipart/form-data" class="form-horizontal" role="form">

				<input type="hidden" name="from_url" value="<?= $from_url ?>">
				<input type="hidden" name="contact_id_val" id="contact_id_val" value="<?php echo $_GET['contactid']?>">
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
						<div class='standard-body-dashboard-content pad-top pad-left pad-right' style="background-color:#fff">
							<?php 
							if($mobile_mode == '1'){
							    if($subtab == 'id_card'){ $display_this = '1'; }else{ $display_this = '0'; }
							    if($subtab != 'id_card'){ $display_this1 = '1'; }else{ $display_this1 = '0'; }
							}else{
							    $display_this = $display_this1 = '1';
							}
							if($display_this) {
								include('../Contacts/contact_profile.php');
								echo '<input type="hidden" name="overview_page" value="1">';
							} 
							if($display_this1) {
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

									$value_config_hidden = [];
									$value_config = array_filter(explode(',',$value_config));
									foreach($value_config as $value_i => $value_field) {
										if(in_array($value_field, $fields_hidden)) {
											$value_config_hidden[] = $value_field;
											unset($value_config[$value_i]);
										}
									}

									$edit_config = '';
									if(edit_visible_function($dbc, 'staff') > 0 || add_visible_function($dbc, 'staff') > 0) {
										$edit_config = $value_config;
										foreach($edit_config as $value_i => $value_field) {
											if(in_array($value_field, $fields_viewonly)) {
												unset($edit_config[$value_i]);
											}
										}
										$edit_config = ','.implode(',',$edit_config).',';
									}
									
									$value_config_hidden = ','.implode(',',$value_config_hidden).',';
									$value_config = ','.implode(',',$value_config).',';
									$contacts_mandatory_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_mandatory_contacts`"))['contacts'];
									if(str_replace(',','',$value_config) != '') {
										?>
<!--   echo ($row_main['subtab'] == $subtab ? '' : 'style="display:none;"');  (in_array($sidebar_fields[$subtab][0], $subtabs_viewonly) || (edit_visible_function($dbc, 'staff') == 0 && add_visible_function($dbc, 'staff') == 0)) ? 'class="viewonly_fields"' : ''  -->
										<?php if($mobile_mode == '1'){ ?>
										    <div <?php echo ($row_main['subtab'] == $subtab ? '' : 'style="display:none;"');  (in_array($sidebar_fields[$subtab][0], $subtabs_viewonly) || (edit_visible_function($dbc, 'staff') == 0 && add_visible_function($dbc, 'staff') == 0)) ? 'class="viewonly_fields"' : ''?>>
										<?php }else{ ?>
										    <div>
										<?php } ?>
											<hr>
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
									if(str_replace(',','',$value_config_hidden) != '') {
										$value_config = $value_config_hidden;
										?>

										<?php if($mobile_mode == '1'){ ?>
										    <div style="display:none">
										<?php }else{ ?>
										    <div>
										<?php } ?>
											<hr>
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
							}//$subtab == 'software_access' && 
							
							if($mobile_mode == '1'){
							    if($subtab == 'software_access' && check_subtab_persmission($dbc, 'staff', ROLE, 'software_access') === TRUE) {
							        include('../Staff/staff_edit_software_access.php');
							    }else if($subtab == 'project') {
							    $value_config = ',Project,';
							    include ('../Contacts/add_contacts_data.php');
							    } else if($subtab == 'ticket') {
							    $value_config = ',Ticket,';
							    include ('../Contacts/add_contacts_data.php');
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
												<th>Dates</th>
												<th>Reason</th>
												<th>Signed</th>
												<th>Function</th>
											</tr>
											<?php while($request = mysqli_fetch_assoc($requests)) {
												$fields = explode('**FFM**',$request['fields']); ?>
												<tr>
													<td data-title="Type of Time Off"><?= $fields[0] == 'Other' ? $fields[1] : $fields[0] ?></td>
													<td data-title="Dates"><?= $fields[2].' - '.$fields[3] ?></td>
													<td data-title="Reason"><?= html_entity_decode($request['desc']) ?></td>
													<td data-title="Signed"><?= get_contact($dbc, $request['contactid']) ?><br /><?= $request['today_date'] ?></td>
													<td data-title=""><a href="../HR/time_off_request/download/hr_<?= $request['fieldlevelriskid'] ?>.pdf">View Request</a><br />
														<?= $request['status'] == 'New' && approval_visible_function($dbc, 'staff') > 0 ? '<a href="../HR/time_off_request/approve_request.php?hrid='.$request['fieldlevelriskid'].'&status=Approved">Approve Request</a><br /><a href="../HR/time_off_request/approve_request.php?hrid='.$request['fieldlevelriskid'].'&status=Denied">Deny Request</a>' : 'Status: '.$request['status'] ?></td>
												</tr>
											<?php } ?>
										</table>
									</div>
								<?php } else {
									echo "<h3>No Requests Found.</h3>";
								}
							} else if($subtab == 'rate_card') {
                                include('edit_staff_rate_card.php');
                            }
							}else{
							    if(check_subtab_persmission($dbc, 'staff', ROLE, 'software_access') === TRUE) {
							        include('../Staff/staff_edit_software_access.php');
							    }
							    $value_config = ',Project,';
							    include ('../Contacts/add_contacts_data.php');
							    $value_config = ',Ticket,';
							    include ('../Contacts/add_contacts_data.php');
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
								<?php //} else if($subtab == 'time_off_requests') {
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
												<th>Dates</th>
												<th>Reason</th>
												<th>Signed</th>
												<th>Function</th>
											</tr>
											<?php while($request = mysqli_fetch_assoc($requests)) {
												$fields = explode('**FFM**',$request['fields']); ?>
												<tr>
													<td data-title="Type of Time Off"><?= $fields[0] == 'Other' ? $fields[1] : $fields[0] ?></td>
													<td data-title="Dates"><?= $fields[2].' - '.$fields[3] ?></td>
													<td data-title="Reason"><?= html_entity_decode($request['desc']) ?></td>
													<td data-title="Signed"><?= get_contact($dbc, $request['contactid']) ?><br /><?= $request['today_date'] ?></td>
													<td data-title=""><a href="../HR/time_off_request/download/hr_<?= $request['fieldlevelriskid'] ?>.pdf">View Request</a><br />
														<?= $request['status'] == 'New' && approval_visible_function($dbc, 'staff') > 0 ? '<a href="../HR/time_off_request/approve_request.php?hrid='.$request['fieldlevelriskid'].'&status=Approved">Approve Request</a><br /><a href="../HR/time_off_request/approve_request.php?hrid='.$request['fieldlevelriskid'].'&status=Denied">Deny Request</a>' : 'Status: '.$request['status'] ?></td>
												</tr>
											<?php } ?>
										</table>
									</div>
								<?php } else {
									echo "<h3>No Requests Found.</h3>";
								}
                                include('edit_staff_rate_card.php');
							}
							
							if($subtab != 'id_card') { ?>
							<!-- <button type='submit' name='<?= $subtab=='rate_card' ? 'submit_rate_card' : 'contactid' ?>' value='<?php echo $contactid; ?>' class="btn brand-btn pull-right">Submit</button> -->
							<button type='submit' name='contactid' value='<?php echo $contactid; ?>' class="btn brand-btn pull-right">Submit</button>
						<?php }
						else if(!isset($_GET['mobile_view']) && vuaed_visible_function($dbc, 'staff') > 0) { ?><a href='?contactid=<?php echo $contactid; ?>&subtab=staff_information' class="hide-on-mobile btn brand-btn pull-right">Edit Staff</a><?php } ?>
						<!-- <a href='<?php echo $from_url; ?>' class="btn brand-btn pull-right">Back</a> -->
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</form>
		</div>
	</div>
</div>
<?php if($_GET['view_only'] != 'id_card') { include_once ('../footer.php'); } ?>
