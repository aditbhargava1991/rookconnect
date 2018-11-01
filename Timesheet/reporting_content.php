<?php include_once('../include.php');
$timesheet_reporting_styling = get_config($dbc,'timesheet_reporting_styling'); ?>

<script type="text/javascript" src="timesheet.js"></script>
<script type="text/javascript">
$(document).on('change', 'select[name="search_staff[]"]', function() { filterStaff(this); });
$(document).on('change', 'select[name="search_group"]', function() { filterStaff(this); });
$(document).on('change', 'select[name="search_security"]', function() { filterStaff(this); });
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
function viewTicket(a) {
  var ticketid = $(a).data('ticketid');
  overlayIFrameSlider('<?= WEBSITE_URL ?>/Ticket/edit_tickets.php?edit='+ticketid+'&calendar_view=true','auto',false,true, $('#timesheet_div').outerHeight()+20);
}
</script>


<form id="form1" name="form1" method="get" enctype="multipart/form-data" class="form-horizontal timesheet_div" role="form">
<div id="dialog-pdf-options" title="Select PDF Fields" style="display: none;">
  <?php echo get_pdf_options($dbc, $timesheet_reporting_styling); ?>
</div>
<input type="hidden" name="tab" value="<?= $_GET['tab'] ?>">
<input type="hidden" name="type" value="<?= $_GET['type'] ?>">
<input type="hidden" name="report" value="<?= $_GET['report'] ?>">
<input type="hidden" name="timesheet_time_format" value="<?= get_config($dbc, 'timesheet_time_format') ?>">
<?php
    $search_staff = '';
    $search_start_date = date('Y-m-01');
    $search_end_date = date('Y-m-d');
    $search_position = '';
    $search_project = '';
    $search_ticket = '';

    if(!empty($_GET['search_staff'])) {
        $search_staff = implode(',',$_GET['search_staff']);
    }
    if(!empty($_GET['search_start_date'])) {
        $search_start_date = $_GET['search_start_date'];
    }
    if(!empty($_GET['search_end_date'])) {
        $search_end_date = $_GET['search_end_date'];
    }
    if(!empty($_GET['search_position'])) {
        $search_position = $_GET['search_position'];
    }
    if(!empty($_GET['search_project'])) {
        $search_project = $_GET['search_project'];
    }
    if(!empty($_GET['search_ticket'])) {
        $search_ticket = $_GET['search_ticket'];
    }
    $current_period = isset($_GET['pay_period']) ? $_GET['pay_period'] : 0;

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

    <?php $search_clearfix = 1; ?>
        <?php if(strpos($field_config, ',search_by_groups,') !== FALSE) { ?>
          <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
            <label for="site_name" class="control-label">Search By Group:</label>
          </div>
            <div class="col-lg-4 col-md-3 col-sm-8 col-xs-12">
              <select data-placeholder="Select a Group" name="search_group" class="chosen-select-deselect form-control">
                <option></option>
                <?php foreach(get_teams($dbc, " AND IF(`end_date` = '0000-00-00','9999-12-31',`end_date`) >= '".date('Y-m-d')."'") as $team) {
                  $team_name = get_team_name($dbc, $team['teamid']);
                  $team_staff = get_team_contactids($dbc, $team['teamid']);
                  if(count($team_staff) > 1) { ?>
                    <option data-staff='<?= json_encode($team_staff) ?>' value="<?= $team_name ?>"><?= $team_name ?></option>
                  <?php }
                } ?>
              </select>
            </div>
            <?php $search_clearfix++ ?>
        <?php } ?>

        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
          <label for="site_name" class="control-label">Search By Staff:</label>
        </div>
          <div class="col-lg-4 col-md-3 col-sm-8 col-xs-12">
              <select multiple data-placeholder="Select Staff Members" name="search_staff[]" class="chosen-select-deselect form-control">
				<option <?= 'ALL' == $search_staff ? 'selected' : '' ?> value="ALL">All Staff</option>
                <?php $query = sort_contacts_query(mysqli_query($dbc,"SELECT distinct(`time_cards`.`staff`), `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`status` FROM `time_cards` LEFT JOIN `contacts` ON `contacts`.`contactid` = `time_cards`.`staff` WHERE `time_cards`.`staff` > 0 AND `contacts`.`deleted`=0".$security_query));
                foreach($query as $staff_row) { ?>
                    <option data-security-level='<?= $staff_row['role'] ?>' data-status="<?= $staff_row['status'] ?>" <?php if (strpos(','.$search_staff.',', ','.$staff_row['contactid'].',') !== FALSE) { echo " selected"; } ?> value='<?php echo  $staff_row['contactid']; ?>' ><?php echo $staff_row['full_name']; ?></option><?php
                } ?>
            </select>
          </div>

        <?= ($search_clearfix%2) == 0 ? '<div class="clearfix"></div>' : '' ?>

        <?php if(strpos($field_config, ',search_by_security,') !== FALSE) { ?>
          <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
            <label for="site_name" class="control-label">Search By Security Level:</label>
          </div>
            <div class="col-lg-4 col-md-3 col-sm-8 col-xs-12">
              <select data-placeholder="Select a Security Level" name="search_security" class="chosen-select-deselect form-control">
                <option></option>
                <?php $on_security = get_security_levels($dbc);
                foreach ($on_security as $security_name => $security_value) { ?>
                  <option value="<?= $security_value ?>"><?= $security_name ?></option>
                <?php } ?>
              </select>
            </div>
            <?php $search_clearfix++ ?>
        <?php } ?>

        <?= ($search_clearfix%2) == 0 ? '<div class="clearfix"></div>' : '' ?>

        <?php if(strpos($field_config, ',search_by_position,') !== FALSE) { ?>
          <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
            <label for="site_name" class="control-label">Search By Position:</label>
          </div>
            <div class="col-lg-4 col-md-3 col-sm-8 col-xs-12">
              <select data-placeholder="Select a Position" name="search_position" class="chosen-select-deselect form-control">
                <option></option>
                <?php $positions = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `positions` WHERE `deleted` = 0 ORDER BY `name`"),MYSQLI_ASSOC);
                foreach ($positions as $position) { ?>
                  <option <?= $search_position == $position['name'] ? 'selected' : '' ?> value="<?= $position['name'] ?>"><?= $position['name'] ?></option>
                <?php } ?>
              </select>
            </div>
            <?php $search_clearfix++ ?>
        <?php } ?>

        <?= ($search_clearfix%2) == 0 ? '<div class="clearfix"></div>' : '' ?>

        <?php if(strpos($field_config, ',search_by_project,') !== FALSE) { ?>
          <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
            <label for="site_name" class="control-label">Search By <?= PROJECT_NOUN ?>:</label>
          </div>
            <div class="col-lg-4 col-md-3 col-sm-8 col-xs-12">
              <select data-placeholder="Select a <?= PROJECT_NOUN ?>" name="search_project" class="chosen-select-deselect form-control">
                <option></option>
                <?php $projects_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`projectid`) FROM `time_cards` WHERE `deleted` = 0"),MYSQLI_ASSOC);
                $all_projects = [];
                foreach($projects_sql as $project_sql) {
                  $project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$project_sql['projectid']."'"));
                  $all_projects[$project['projectid']] = get_project_label($dbc, $project);
                }
                asort($all_projects);
                foreach ($all_projects as $projectid => $project_label) { ?>
                  <option <?= $search_project == $projectid ? 'selected' : '' ?> value="<?= $projectid ?>"><?= $project_label ?></option>
                <?php } ?>
              </select>
            </div>
            <?php $search_clearfix++ ?>
        <?php } ?>

        <?= ($search_clearfix%2) == 0 ? '<div class="clearfix"></div>' : '' ?>

        <?php if(strpos($field_config, ',search_by_ticket,') !== FALSE) { ?>
          <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
            <label for="site_name" class="control-label">Search By <?= TICKET_NOUN ?>:</label>
          </div>
            <div class="col-lg-4 col-md-3 col-sm-8 col-xs-12">
              <select data-placeholder="Select a <?= TICKET_NOUN ?>" name="search_ticket" class="chosen-select-deselect form-control">
                <option></option>
                <?php $tickets_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`ticketid`) FROM `time_cards` WHERE `deleted` = 0"),MYSQLI_ASSOC);
                $all_tickets = [];
                foreach($tickets_sql as $ticket_sql) {
                  $ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$ticket_sql['ticketid']."'"));
                  $all_tickets[$ticket['ticketid']] = get_ticket_label($dbc, $ticket);
                }
                asort($all_tickets);
                foreach ($all_tickets as $ticketid => $ticket_label) { ?>
                  <option <?= $search_ticket == $ticketid ? 'selected' : '' ?> value="<?= $ticketid ?>"><?= $ticket_label ?></option>
                <?php } ?>
              </select>
            </div>
            <?php $search_clearfix++ ?>
        <?php } ?>

        <?= ($search_clearfix%2) == 0 ? '<div class="clearfix"></div>' : '' ?>

        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
          <label for="site_name" class="control-label">Search By Start Date:</label>
        </div>
          <div class="col-lg-4 col-md-3 col-sm-8 col-xs-12">
              <input style="width: 100%;" name="search_start_date" value="<?php echo $search_start_date; ?>" type="text" class="form-control datepicker">
          </div>

        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
          <label for="site_name" class="control-label">Search By End Date:</label>
        </div>
          <div class="col-lg-4 col-md-3 col-sm-8 col-xs-12">
              <input style="width: 100%;" name="search_end_date" value="<?php echo $search_end_date; ?>" type="text" class="form-control datepicker">
          </div>


        <div class="col-sm-12 pull-right">
    			<div class="pull-right double-gap-left" style="height:1.5em; margin:0.5em;">

                <?php
                $timesheet_reporting_styling = get_config($dbc,'timesheet_reporting_styling');
                if($timesheet_reporting_styling == 'EGS') {
                $search_staff_query = implode('search_staff%5B%5D=', explode(',',$search_staff)); ?>

                <a target="_blank" href="<?= WEBSITE_URL ?>/Timesheet/reporting.php?export=pdf_egs&search_staff=<?php echo $search_staff; ?>&search_start_date=<?php echo $search_start_date; ?>&search_end_date=<?php echo $search_end_date; ?>&search_position=<?php echo $search_position; ?>&search_project=<?php echo $search_project; ?>&search_ticket=<?php echo $search_ticket; ?>&see_staff=<?= $_GET['see_staff'] ?>&timesheet_tab=reporting" onclick="displayPDFOptions(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/pdf.png" style="height:100%; margin:0;" class="no-toggle" title="PDF" /></a>


                <?php } else { ?>
                <a target="_blank" href="<?= WEBSITE_URL ?>/Timesheet/reporting.php?export=pdf&search_staff=<?php echo $search_staff; ?>&search_start_date=<?php echo $search_start_date; ?>&search_end_date=<?php echo $search_end_date; ?>&search_position=<?php echo $search_position; ?>&search_project=<?php echo $search_project; ?>&search_ticket=<?php echo $search_ticket; ?>" onclick="displayPDFOptions(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/pdf.png" style="height:100%; margin:0;" class="no-toggle" title="PDF" /></a>

                <?php } ?>

                <a target="_blank" href="<?= WEBSITE_URL ?>/Timesheet/excel_reporting.php?export=reporting_excel&search_staff=<?php echo $search_staff; ?>&search_start_date=<?php echo $search_start_date; ?>&search_end_date=<?php echo $search_end_date; ?>&search_position=<?php echo $search_position; ?>&search_project=<?php echo $search_project; ?>&search_ticket=<?php echo $search_ticket; ?>" onclick="displayPDFOptions(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/POS_XSL.png" style="height:100%; margin:0;" class="no-toggle" title="Excel" /></a>

    			<!--
                - <a href="<?= WEBSITE_URL ?>/Timesheet/time_cards.php?export=csv" title="CSV"><img src="<?php echo WEBSITE_URL; ?>/img/csv.png" style="height:100%; margin:0;" /></a>
                -->
                </div>
          <div class="form-group">
            <a href="?tab=<?= $_GET['tab'] ?>&pay_period=<?= $current_period + 1 ?>&search_site=<?= $search_site ?>&search_project=<?= $search_project ?>&search_ticket=<?= $search_ticket ?>&search_staff[]=<?= $search_staff ?>&type=<?= $_GET['type'] ?>&report=<?= $_GET['report'] ?>" name="display_all_inventory" class="btn brand-btn mobile-block pull-right">Next <?= $pay_period_label ?></a>
            <a href="?tab=<?= $_GET['tab'] ?>&pay_period=<?= $current_period - 1 ?>&search_site=<?= $search_site ?>&search_project=<?= $search_project ?>&search_ticket=<?= $search_ticket ?>&search_staff[]=<?= $search_staff ?>&type=<?= $_GET['type'] ?>&report=<?= $_GET['report'] ?>" name="display_all_inventory" class="btn brand-btn mobile-block pull-right">Prior <?= $pay_period_label ?></a>
            <a href="?" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block pull-right" onclick="$('[name^=search_staff]').find('option').prop('selected',false); $('[name^=search_staff]').find('option[value=ALL]').prop('selected',true).change(); $('[name=search_user_submit]').click(); return false;">Display All</a>
            <button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block pull-right">Search</button>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
      </form>
<br><br><br>

     <form id="form1" name="form1" method="get" class="form-horizontal" role="form">

    <div id="no-more-tables">
		<?php
        if($timesheet_reporting_styling == 'EGS') {
            echo get_egs_hours_report($dbc, $search_staff, $search_start_date, $search_end_date,$search_staff);
        } else {
            echo get_hours_report($dbc, $search_staff, $search_start_date, $search_end_date, $search_position, $search_project, $search_ticket, '', $config['hours_types']);
        }
        if(in_array('summary',explode(',',get_config($dbc, 'timesheet_report_options')))) {
            echo get_hours_report_summary($dbc, $search_staff, $search_start_date, $search_end_date, $search_position, $search_project, $search_ticket, '', $config['hours_types']);
        }
        ?>

	</div>

	</form>

