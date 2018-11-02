<?php include('../include.php');
include_once('../Timesheet/reporting_functions.php');
include('../Calendar/calendar_functions_inc.php'); ?>
<script type="text/javascript" src="timesheet.js"></script>
<script type="text/javascript">
$(document).on('change', 'select[name="search_staff[]"]', function() { filterStaff(this); });
$(document).on('change', 'select[name="search_group"]', function() { filterStaff(this); });
//$(document).on('change', 'select[name="search_security"]', function() { filterStaff(this); });
function filterStaff(sel) {
  var staff_sel = $('select[name="search_staff[]"');
  if(sel.name == "search_staff[]") {
    if($(staff_sel).val().indexOf('ALL') > -1) {
      $(staff_sel).find('option').prop('selected', false);
      $(staff_sel).find('option').filter(function() { return $(this).val() > 0 && $(this).data('status') > 0; }).prop('selected', true);
      $(staff_sel).trigger('change.select2');
    }
  } else if(sel.name == "search_group") {
    if($(sel).val() != '') {
      var staff = $(sel).find('option:selected').data('staff');
      if(staff.length == 0) {
        staff = [''];
      }
      $(staff_sel).find('option').prop('selected', false);
      staff.forEach(function(staffid) {
        $(staff_sel).find('option').filter(function() { return $(this).val() == staffid }).prop('selected', true);
        $(staff_sel).trigger('change.select2');
      });
    }
  } else if(sel.name == "search_security") {
    if($(sel).val() != '') {
      var security_level = $(sel).val();
      $(staff_sel).find('option').prop('selected', false);
      $(staff_sel).find('option').each(function() {
        if($(this).val() > 0) {
          var security_levels = ','+$(this).data('security-level')+',';
          if(security_levels.indexOf(security_level) > -1) {
            $(this).prop('selected', true);
          }
        }
      });
      $(staff_sel).trigger('change.select2');
    }
  }
}
function unapproveTimeSheet(a) {
    var staff = $(a).data('staff');
    var type = $(a).data('type');
    var date = $(a).data('date');
    var id = $(a).data('timesheetid');
    if(confirm('Are you sure you want to unapprove this time?')) {
        $.ajax({
            url: '../Timesheet/time_cards_ajax.php?action=unapprove_time',
            method: 'POST',
            data: { staff: staff, type: type, date: date, id: id },
            success: function(response) {
                console.log(response);
                $(a).closest('tr').remove();
            }
        });
    }
}
</script>

</head>
<body>
<?php
include_once ('../navigation.php');
checkAuthorised('timesheet');
include 'config.php';
$value = $config['settings']['Choose Fields for Time Sheets Dashboard'];
$field_config = get_field_config($dbc, 'time_cards_dashboard'); ?>

<script type="text/javascript">
function viewTicket(a) {
	var ticketid = $(a).data('ticketid');
	overlayIFrameSlider('<?= WEBSITE_URL ?>/Ticket/edit_tickets.php?edit='+ticketid+'&calendar_view=true','auto',false,true, $('#timesheet_div').outerHeight());
}
</script>

<div class="container" id="timesheet_div">
    <div id="dialog-pdf-options" title="Select PDF Fields" style="display: none;">
        <?php echo get_pdf_options($dbc, (get_config($dbc,'timesheet_payroll_styling') == 'Both' && $_GET['subtab'] != 'Detailed' ? 'EGS' : get_config($dbc, 'timesheet_payroll_styling')),  'payroll'); ?>
    </div>
	<div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="timesheet_iframe" src=""></iframe>
		</div>
	</div>
    <div class="row timesheet_div">
        <input type="hidden" name="timesheet_time_format" value="<?= get_config($dbc, 'timesheet_time_format') ?>">
        <div class="col-md-12">

        <h1 class="">Payroll Dashboard
        <?php
        if(config_visible_function_custom($dbc)) {
            echo '<a href="field_config.php?from_url=payroll.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
        }
        ?></h1>

        <form id="form1" name="form1" method="GET" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="tab" value="<?= $_GET['tab'] ?>">
            <input type="hidden" name="subtab" value="<?= $_GET['subtab'] ?>">
            <?php echo get_tabs('Payroll', $_GET['tab'], array('db' => $dbc, 'field' => $value['config_field'])); ?>
            <?php if(get_config($dbc,'timesheet_payroll_styling') == 'Both') {
                $query_string = $_GET;
                unset($query_string['subtab']); ?>
                <br>
                <div>
                    <a href="payroll.php?<?= http_build_query($query_string) ?>&subtab=Summary"><button type="button" class="btn brand-btn mobile-block <?= (empty($_GET['subtab']) || $_GET['subtab'] == 'Summary' ? 'active_tab' : '') ?>">Summary</button></a>
                    <a href="payroll.php?<?= http_build_query($query_string) ?>&subtab=Detailed"><button type="button" class="btn brand-btn mobile-block <?= ($_GET['subtab'] == 'Detailed' ? 'active_tab' : '') ?>">Detailed</button></a>
                </div>
            <?php } ?>
            <br><br>
            <?php
                $timesheet_payroll_styling = get_config($dbc,'timesheet_payroll_styling');
                if($timesheet_payroll_styling == 'Both') {
                    $timesheet_payroll_styling = 'EGS';
                }
                if($_GET['subtab'] == 'Detailed') {
                    $timesheet_payroll_styling = 'Default';
                }

                $highlight = get_config($dbc, 'timesheet_highlight');
                $mg_highlight = get_config($dbc, 'timesheet_manager');
                $search_site = '';
                $search_staff_list = '';
                $search_start_date = date('Y-m-01');
                $search_end_date = date('Y-m-d');
                $position = '';

                if(!empty($_GET['search_site'])) {
                    $search_site = $_GET['search_site'];
                }
                if(!empty($_GET['search_staff'])) {
					$search_staff_list = array_filter($_GET['search_staff']);
                }

                if(!empty($_GET['search_start_date'])) {
                    $search_start_date = $_GET['search_start_date'];
                }
                if(!empty($_GET['search_end_date'])) {
                    $search_end_date = $_GET['search_end_date'];
                }
                $timesheet_comment_placeholder = get_config($dbc, 'timesheet_comment_placeholder');
                $timesheet_start_tile = get_config($dbc, 'timesheet_start_tile');
                $timesheet_time_format = get_config($dbc, 'timesheet_time_format');
                $timesheet_rounding = get_config($dbc, 'timesheet_rounding');
                $timesheet_rounded_increment = get_config($_SERVER['DBC'], 'timesheet_rounded_increment') / 60;
                $current_period = !empty($_GET['pay_period']) || $_GET['pay_period'] == 0 ? $_GET['pay_period'] : -1;
                $_GET['pay_period'] = $current_period;
                include('pay_period_dates.php');

                $timesheet_security_roles = array_filter(explode(',',get_config($dbc, 'timesheet_security_roles')));
                if(!empty($timesheet_security_roles)) {
                    $security_query = [];
                    foreach($timesheet_security_roles as $security_role) {
                        $security_query[] =  "CONCAT(',',`contacts`.`role`,',') LIKE '%,$security_role,%'";
                    }
                    $security_query = " AND (".implode(" OR ", $security_query).")";
                }
                ?>

			<?php if(strpos($field_config, ',search_by_groups,') !== FALSE) { ?>
			  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
				<label for="site_name" class="control-label">Search By Group:</label>
			  </div>
				<div class="col-lg-4 col-md-3 col-sm-8 col-xs-12">
				  <select data-placeholder="Select a Group" name="search_group" class="chosen-select-deselect form-control">
					<option></option>
					<?php foreach(explode('#*#',get_config($dbc, 'ticket_groups')) as $group) {
					  $group = explode(',',$group);
					  $group_name = $group[0];
					  $group_staff = [];
					  foreach ($group as $staff) {
						if ($staff > 0) {
						  $group_staff[] = $staff;
						}
					  }
					  if(count($group) > 1) { ?>
						<option data-staff='<?= json_encode($group_staff) ?>' value="<?= $group_name ?>"><?= $group_name ?></option>
					  <?php }
					} ?>
				  </select>
				</div>
				<?php $search_clearfix++ ?>
			<?php } ?>

            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
                <label class="control-label">Search By Staff:</1label>
            </div>
            <div class="col-lg-4 col-md-3 col-sm-8 col-xs-12">
                <?php if($timesheet_payroll_styling == 'EGS') { ?>
                    <select multiple data-placeholder="Select Staff Members" name="search_staff[]" class="chosen-select-deselect form-control">
                        <option <?= in_array('ALL',$search_staff_list) ? 'selected' : '' ?> value="ALL">All Staff</option>
						<?php $query = sort_contacts_query(mysqli_query($dbc,"SELECT distinct(`time_cards`.`staff`), `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`status` FROM `time_cards` LEFT JOIN `contacts` ON `contacts`.`contactid` = `time_cards`.`staff` WHERE `time_cards`.`staff` > 0 AND `contacts`.`deleted`=0".$security_query));
                        $prev_staff = '';
                        $next_staff = '';
						foreach($query as $key => $staff_row) {
                            if(in_array($staff_row['contactid'], $search_staff_list) && empty($next_staff) && empty($prev_staff)) {
                                $keys = array_keys($query);
                                $prev_staff = $query[$keys[array_search($key, $keys)-1]]['contactid'];
                                $next_staff = $query[$keys[array_search($key, $keys)+1]]['contactid'];
                            } ?>
							<option data-security-level='<?= $staff_row['role'] ?>' data-status="<?= $staff_row['status'] ?>" <?php if (in_array($staff_row['contactid'],$search_staff_list) !== FALSE && $search_staff_list != '') { echo " selected"; } ?> value='<?php echo  $staff_row['contactid']; ?>' ><?php echo $staff_row['full_name']; ?></option><?php
							if(in_array('ALL',$search_staff_list)) {
                                $search_staff_list[] = $staff_id['contactid'];
                            }
						} ?>
                    </select>
                <?php } else { ?>
                    <select data-placeholder="Select Staff Members" multiple name="search_staff[]" class="chosen-select-deselect form-control" onchange="$('[name=search_start_date]').val('');$('[name=search_end_date]').val('');">
                        <option value="ALL_STAFF">Select All Staff</option><?php
                        $query = mysqli_query($dbc,"SELECT `supervisor`, `position`, `staff_list`, `security_level_list` FROM `field_config_supervisor` WHERE `supervisor`='".$_SESSION['contactid']."' OR (SELECT CONCAT(',',`staff_list`,',') FROM `field_config_supervisor` WHERE `supervisor`='".$_SESSION['contactid']."' AND `position` = 'Manager') LIKE CONCAT('%,',`supervisor`,',%')");
                        $staff_members = [];
                        if(mysqli_num_rows($query) > 0) {
                            while($row1 = mysqli_fetch_array($query)) {
                                if($row1['supervisor'] == $_SESSION['contactid']) {
                                    $position = $row1['position'];
                                }
                                $staff_members = array_unique(array_merge($staff_members, array_filter(explode(',',$row1['staff_list']))));
                                $security_levels = array_filter(explode(',',$row1['security_level_list']));
                                if(!empty($security_levels)) {
                                    foreach($security_levels as $security_level) {
                                        if(!empty($security_level)) {
                                            $staff_with_security = array_column(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted` = 0 AND `status` > 0 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND CONCAT(',',`role`,',') LIKE '%,".$security_level.",%'")),'contactid');
                                            $staff_members = array_unique(array_merge($staff_members, array_filter($staff_with_security)));
                                        }
                                    }
                                }
                            }
                            $staff_members_ids = $staff_members;
                            $staff_members = [];
                            foreach($staff_members_ids as $staff_members_id) {
                                $staff_members[] = ['contactid' => $staff_members_id, 'full_name' => get_contact($dbc,$staff_members_id)];
                            }
                        } else {
                            $staff_members = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted` = 0 AND `status` > 0 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.$security_query));
                        }
                        $prev_staff = '';
                        $next_staff = '';
                        foreach($staff_members as $key => $staff_id) {
                            if(in_array($staff_id['contactid'], $search_staff_list) && empty($next_staff) && empty($prev_staff)) {
                                $keys = array_keys($staff_members);
                                $prev_staff = $staff_members[$keys[array_search($key, $keys)-1]]['contactid'];
                                $next_staff = $staff_members[$keys[array_search($key, $keys)+1]]['contactid'];
                            } ?>
                            <option <?php if (in_array($staff_id['contactid'], $search_staff_list) || in_array('ALL_STAFF',$search_staff_list)) { echo " selected"; } ?> value='<?php echo $staff_id['contactid']; ?>'><?php echo $staff_id['full_name']; ?></option><?php
                            if(in_array('ALL_STAFF',$search_staff_list)) {
                                $search_staff_list[] = $staff_id['contactid'];
                            }
                        } ?>
                    </select>

                <?php } ?>
            </div>

            <?php if($timesheet_payroll_styling == 'Default') { ?>
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
                    <label for="site_name" class="control-label">Search By Site:</label>
                </div>
                <div class="col-lg-4 col-md-9 col-sm-8 col-xs-8">
                    <select data-placeholder="Select a Site" name="search_site" class="chosen-select-deselect form-control">
                        <option value=""></option><?php
                        $query = mysqli_query($dbc,"SELECT `contactid`, CONCAT(IFNULL(`site_name`,''),IF(IFNULL(`site_name`,'') != '' AND IFNULL(`display_name`,'') != '',': ',''),IFNULL(`display_name`,'')) display_name FROM `contacts` WHERE `category`='Sites' AND `deleted`=0");
                        while($row1 = mysqli_fetch_array($query)) { ?>
                            <option <?php if ($row1['contactid'] == $search_site) { echo " selected"; } ?> value='<?php echo  $row1['contactid']; ?>' ><?php echo $row1['display_name']; ?></option><?php
                        } ?>
                    </select>
                </div>
            <?php } ?>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
                    <label for="site_name" class="control-label">Search By Start Date:</label>
                </div>
                <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
                    <input name="search_start_date" value="<?php echo $search_start_date; ?>" type="text" class="form-control datepicker">
                </div>

                <div class="clearfix visible-xs"></div>

                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
                    <label for="site_name" class="control-label">Search By End Date:</label>
                </div>
                <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
                    <input name="search_end_date" value="<?php echo $search_end_date; ?>" type="text" class="form-control datepicker">
                </div>
            </div>

            <div class="form-group gap-top">
                <div class="text-right">
                    <?php $search_staff_query = "search_staff%5B%5D=".implode('&search_staff%5B%5D=', $search_staff_list); ?>
					<a href="?tab=<?= $_GET['tab'] ?>&subtab=<?= $_GET['subtab'] ?>&pay_period=<?= $current_period + 1 ?>&search_site=<?= $search_site ?>&<?= $search_staff_query ?>&see_staff=<?= $_GET['see_staff'] ?>" name="display_all_inventory" class="btn brand-btn mobile-block pull-right">Next <?= $pay_period_label ?></a>
					<a href="?tab=<?= $_GET['tab'] ?>&subtab=<?= $_GET['subtab'] ?>&pay_period=<?= $current_period - 1 ?>&search_site=<?= $search_site ?>&<?= $search_staff_query ?>&see_staff=<?= $_GET['see_staff'] ?>" name="display_all_inventory" class="btn brand-btn mobile-block pull-right">Prior <?= $pay_period_label ?></a>
                    <button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
                    <button type="button" onclick="$('[name^=search_staff]').find('option').prop('selected',false); $('[name^=search_staff]').find('option[value=<?= $timesheet_payroll_styling == 'EGS' ? 'ALL' : 'ALL_STAFF' ?>]').prop('selected',true).change(); $('[name=search_user_submit]').click(); return false;" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button><?php

                    if($timesheet_payroll_styling == 'EGS') { ?>
                        <a target="_blank" href="<?= WEBSITE_URL ?>/Timesheet/reporting.php?export=pdf_egs&search_staff=<?php echo (!empty($_GET['see_staff']) ? $_GET['see_staff'] : implode(',', $search_staff_list)); ?>&search_start_date=<?php echo $search_start_date; ?>&search_end_date=<?php echo $search_end_date; ?>&search_position=<?php echo $search_position; ?>&search_project=<?php echo $search_project; ?>&search_ticket=<?php echo $search_ticket; ?>&tab=<?= $_GET['tab'] ?>&see_staff=<?= $_GET['see_staff'] ?>&timesheet_tab=payroll" onclick="displayPDFOptions(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/pdf.png" style="height:100%; margin:0;" class="no-toggle" title="PDF" /></a><?php
                    } ?>

                <a target="_blank" href="<?= WEBSITE_URL ?>/Timesheet/excel_reporting.php?export=payroll_excel&search_staff=<?php echo (!empty($_GET['see_staff']) ? $_GET['see_staff'] : implode(',', $search_staff_list)); ?>&search_start_date=<?php echo $search_start_date; ?>&search_end_date=<?php echo $search_end_date; ?>&search_position=<?php echo $search_position; ?>&search_project=<?php echo $search_project; ?>&search_ticket=<?php echo $search_ticket; ?>&see_staff=<?= $_GET['see_staff'] ?>" onclick="displayPDFOptions(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/POS_XSL.png" style="height:1.5em; margin:0.5em;" class="no-toggle" title="Excel" /></a>

                <?php if(count($search_staff_list) == 1 && $search_staff_list[0] != 'ALL_STAFF' && !empty($search_staff_list)) { ?>
                    <div class="clearfix"></div>
                    <a href="?tab=<?= $_GET['tab'] ?>&subtab=<?= $_GET['subtab'] ?>&pay_period=<?= $current_period ?>&search_site=<?= $search_site ?>&search_staff[]=<?= $next_staff ?>&see_staff=<?= !empty($_GET['see_staff']) ? $next_staff : '' ?>" class="btn brand-btn mobile-block pull-right">Next Staff</a>
                    <a href="?tab=<?= $_GET['tab'] ?>&subtab=<?= $_GET['subtab'] ?>&pay_period=<?= $current_period ?>&search_site=<?= $search_site ?>&search_staff[]=<?= $prev_staff ?>&see_staff=<?= !empty($_GET['see_staff']) ? $prev_staff : '' ?>" class="btn brand-btn mobile-block pull-right">Previous Staff</a>
                <?php } ?>
                </div>
            </div>
        </form>

    <form id="form1" name="form1" action="add_time_card_approvals.php?tab=<?= $_GET['tab'] ?>&subtab=<?= $_GET['subtab'] ?>&pay_period=<?= $_GET['pay_period'] ?>&search_start_date=<?= $_GET['search_start_date'] ?>&search_end_date=<?= $_GET['search_end_date'] ?>&search_site=<?= $_GET['search_site'] ?>" method="POST" enctype="multipart/form-data" class="form-horizontal" role="form">

    <div id="no-more-tables">

    <?php
    if($timesheet_payroll_styling == 'EGS') {
        if(!empty($_GET['see_staff'])) {
            echo get_egs_hours_report($dbc, $search_staff_list,$search_start_date, $search_end_date,$_GET['see_staff'], '', 'payroll');
        } else {
            echo get_egs_main_hours_report($dbc, $search_staff_list, $search_start_date, $search_end_date, '', 'payroll');
        }
    } else {

        $grid = '';
        $printable_grid = '';

        $tb_field = $value['config_field'];

        if(!empty($search_staff_list)) {
            foreach(array_filter(array_unique($search_staff_list), function($id) { return $id != 'ALL_STAFF'; }) as $search_staff) {
                echo '<div class="status_group">';
                if(count($search_staff_list) > 1) {
                    echo "<h2>".get_contact($dbc, $search_staff)."<img src='../img/empty.png' class='statusIcon inline-img no-toggle no-margin'></h2>";
                }
                $filter = ' AND (staff = "'.$search_staff.'")';
                if($search_site != '') {
                    $filter .= ' AND (business = "'.$search_site.'")';
                }
                if($search_start_date != '') {
                    $filter .= ' AND `date` >= "'.$search_start_date.'"';
                }
                if($search_end_date != '') {
                    $filter .= ' AND `date` <= "'.$search_end_date.'"';
                }


                $query_check_credentials = 'SELECT * FROM time_cards WHERE (approv = "Y" OR approv = "P") AND `deleted`=0 '.$filter;

                $result = mysqli_query($dbc, $query_check_credentials);
                $layout = get_config($dbc, 'timesheet_layout');
                $schedule = mysqli_fetch_array(mysqli_query($dbc, "SELECT `scheduled_hours`, `schedule_days` FROM `contacts` WHERE `contactid`='$search_staff'"));
                $schedule_hrs = explode('*',$schedule['scheduled_hours']);
                $schedule_days = explode('*',$schedule['schedule_days']);
                $schedule_list = [0=>'---',1=>'---',2=>'---',3=>'---',4=>'---',5=>'---',6=>'---'];
                foreach($schedule_days as $key => $day_of_week) {
                    $schedule_list[$day_of_week] = $schedule_hrs[$key];
                }

                echo '<input type="hidden" name="supervisor_id" value="'.$_SESSION['contactid'].'">';
                echo '<input type="hidden" name="supervisor" value="'.$position.'">';
                echo '<input type="hidden" name="staff_id" value="'.$search_staff.'">';
                echo '<input type="hidden" name="site_id" value="'.$search_site.'">'; ?>
                <?php if(in_array($layout, ['', 'multi_line', 'position_dropdown', 'ticket_task'])):
                    echo '<button type="submit" name="approv_db" value="update_btn" class="btn brand-btn pull-right">Update Hours</button>';
                    echo '<button type="submit" name="approv_db" value="paid_btn" class="btn brand-btn pull-right">Update and Mark Selected Paid</button>';
                    include('timesheet_display.php');
                    echo '<div class="col-sm-2 pull-right">';
                        echo '<button type="submit" name="approv_db" value="update_btn" class="btn brand-btn pull-right">Update Hours</button>';
                        echo '<button type="submit" name="approv_db" value="paid_btn" class="btn brand-btn pull-right">Update and Mark Selected Paid</button>';
                    echo '</div>';
                elseif($layout == 'rate_card' || $layout == 'rate_card_tickets'):
                    echo '<button type="submit" value="rate_approval" name="submit" class="btn brand-btn mobile-block pull-right">Update and Mark Selected Paid</button>';
                    echo '<button type="submit" value="rate_timesheet" name="submit" class="btn brand-btn mobile-block pull-right">Update Hours</button>';
                    for($date = $search_start_date; strtotime($date) <= strtotime($search_end_date); $date = date("Y-m-d", strtotime("+1 day", strtotime($date)))) {
                        if($layout == 'rate_card_tickets') {
                            $ticket_sql = "SELECT `tickets`.*, `osbn`.`item_id` `osbn` FROM `tickets` LEFT JOIN `ticket_attached` `osbn` ON `tickets`.`ticketid`=`osbn`.`ticketid` AND `osbn`.`src_table`='Staff' AND `osbn`.`deleted`=0 AND `osbn`.`position`='Team Lead' WHERE `tickets`.`ticketid` IN (SELECT `ticketid` FROM `time_cards` WHERE `deleted`=0 AND `staff`='$search_staff' AND `date`='$date' UNION SELECT `ticketid` FROM `tickets` WHERE CONCAT(',',`contactid`,',') LIKE '%,$search_staff,%' AND (`to_do_date`='$date' OR '$date' BETWEEN `to_do_date` AND `to_do_end_date` OR `internal_qa_date`='$date' OR `deliverable_date`='$date') AND `deleted`=0)";
                        } else {
                            $ticket_sql = "SELECT 0 `ticketid`";
                        }
                        $work_hours_sql = "SELECT IFNULL(SUM(`total_hrs`),0) hours, `category`, `work_desc`, `hourly` FROM `staff_rate_table` staff LEFT JOIN `time_cards` sheet ON CONCAT(',',staff.`staff_id`,',') LIKE CONCAT('%,',sheet.`staff`,',%') AND sheet.`type_of_time`=staff.`work_desc` AND sheet.`date`='$date' WHERE CONCAT(',',staff.`staff_id`,',') LIKE '%,$search_staff,%' AND staff.`deleted`=0 AND sheet.`deleted`=0 AND DATE(NOW()) BETWEEN staff.`start_date` AND IFNULL(NULLIF(staff.`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `category`, `work_desc` ORDER BY `category`, `work_desc`, `hourly`";
                        $work_result = mysqli_query($dbc, $work_hours_sql);
                        $day_of_week = date('l', strtotime($date));
                        $shifts = checkShiftIntervals($dbc, $search_staff, $day_of_week, $date);
                        if(!empty($shifts)) {
                            $shift = '';
                            foreach ($shifts as $shift_detail) {
                                $shift .= $shift_detail['starttime'].' - '.$shift_detail['endtime'].'<br>';
                            }
                        } else {
                            $shift = $schedule_list[date('w',strtotime($date))];
                        }
                        echo "<div class='form-group' style='border:solid black 1px; display:inline-block; margin:1em; width:25em;'>";
                        echo "<div style='border:solid black 1px; padding:0.35em; width: 25em;'><div style='display:inline-block; width:12em;'>Date:</div><div style='display:inline-block; width:12em;'>";
                        echo "<label style='width:12em;'>$date<input type='checkbox' name='approve_date[]' value='$date' class='pull-right' /></label></div>";
                        if($shift != '') {
                            echo "<div style='display:inline-block; width:12em;'>Hours:</div><div style='display:inline-block; width:12em;'>$shift</div>";
                        }
                        if($ticket['ticketid'] > 0) {
                            echo "<div style='display:inline-block; width:12em;'>".TICKET_NOUN.":</div><div style='display:inline-block; width:16em;'>".get_ticket_label($dbc, $ticket).($ticket['osbn'] > 0 ? "<br />OSBN: ".get_contact($dbc, $ticket['osbn']) : '')."</div>";
                        }
                        echo "</div>";
                        $category = '';
                        while($hours = mysqli_fetch_array($work_result)) {
                            if($hours['category'] != $category) {
                                if($category != '') {
                                    echo "<div style='display:inline-block; width:12em;'>$category Description</div><div style='display:inline-block; width:12em;'><input type='hidden' name='comment_date[]' value='$date'><input type='hidden' name='comment_cat[]' value='$category'><input type='text' name='cat_comment[]' class='form-control'></div></div>";
                                }
                                $category = $hours['category'];
                                echo "<div style='border:solid black 1px; padding:0.35em; width: 25em;'><div style='display:inline-block; width:12em;'>$category</div><div style='display:inline-block; text-align:center; width:6em;'>Hours</div><div style='display:inline-block; text-align:center; width:6em;'>Rate</div>";
                            }
                            echo "<div style='display:inline-block; width:12em;'>".$hours['work_desc']."</div><div style='display:inline-block; width:6em;'>";
                            echo "<input type='hidden' name='hours_cat[]' value='$category'><input type='hidden' name='hours_type[]' value='".$hours['work_desc']."'><input type='hidden' name='hours_date[]' value='$date'>";
                            echo "<input type='text' name='hours[]' class='form-control' value='".$hours['hours']."'></div><div style='display:inline-block; text-align:center; width:6em;'>$".$hours['hourly']."</div>";
                            if($hours['comment_box'] != '' && in_array(['Comments','text','comment_box'],$config['settings']['Choose Fields for Time Sheets']['data']['General'])) {
                                echo html_entity_decode($hours['comment_box']);
                            }
                        }
                        if($category != '') {
                            echo "<div style='display:inline-block; width:12em;'>$category Description</div><div style='display:inline-block; width:12em;'><input type='hidden' name='comment_date[]' value='$date'><input type='hidden' name='comment_cat[]' value='$category'><input type='text' name='cat_comment[]' class='form-control'></div></div>";
                        }
                        echo "</div>";
                    }
                    if(vuaed_visible_function_custom($dbc) && $search_staff != ''):
                        echo '<div class="clearfix"></div>';
                        echo '<button type="submit" value="rate_approval" name="submit" class="btn brand-btn mobile-block pull-right">Update and Mark Selected Paid</button>';
                        echo '<button type="submit" value="rate_timesheet" name="submit" class="btn brand-btn mobile-block pull-right">Update Hours</button>';
                    endif;
                endif;
                echo '</div>';
            }
        } else {
            echo "<h3>Please select a staff member.</h3>";
        }
    }

    ?>
    </div>

</div>
</form>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
