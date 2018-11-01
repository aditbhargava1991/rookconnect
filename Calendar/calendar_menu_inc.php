<?php include_once('calendar_settings_inc.php'); ?>
<div class="calendar-menu">
	<span id="calendar_date_heading" style="font-size: 2em;">&nbsp;&nbsp;<?= $date_string ?></span>
    <div class="pull-right mobile-clear-floats">
        <?php
        if($export_time_table == 1) { ?>
                <a href='' onclick='overlayIFrameSlider("<?= WEBSITE_URL ?>/Calendar/export_time_table.php"); return false;' class="block-label pull-right">Export Time Table</a>
        <?php }
        if((($_GET['type'] == 'event' && vuaed_visible_function($dbc, 'calendar_rook')) || ($wait_list == 'ticket' && $new_ticket_button !== '')) && $edit_access == 1) {
            if($_GET['type'] == 'schedule') { ?>
                <a href='' onclick='dispatchNewWorkOrder(this); return false;' class="block-label pull-right">New <?= TICKET_NOUN ?></a><?php
            } else { ?>
                <a href='<?= WEBSITE_URL ?>/Ticket/index.php?edit=0' onclick='overlayIFrameSlider(this.href+"&calendar_view=true"); return false;' class="block-label pull-right">New <?= TICKET_NOUN ?></a><?php
            }
        }
        if($use_shifts !== '' && $_GET['mode'] != 'staff_summary' && $_GET['mode'] != 'ticket_summary' && $_GET['mode'] != 'client') {
            $shift_fields = ','.mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts`"))['enabled_fields'].',';
            if(strpos($shift_fields, ',shifts_report,') !== FALSE) { ?>
                <a href="" onclick='overlayIFrameSlider("<?= WEBSITE_URL ?>/Calendar/shifts_report.php?region=<?= $_GET['region'] ?>"); return false;' class="block-label pull-right shift_btn" <?= $_GET['type'] != 'shift' ? 'style="display:none;"' : '' ?>>Shifts Report</a>
            <?php }
            if(strpos($shift_fields, ',export_button,') !== FALSE) { ?>
                <script type="text/javascript">
                function exportShifts() {
                    $.ajax({
                        type: 'GET',
                        url: '../Calendar/calendar_ajax_all.php?fill=export_shifts',
                        dataType: 'html',
                        success: function(response) {
                            window.open(response, '_blank');
                        }
                    });
                }
                </script>
                <a href="" onclick="exportShifts(); return false;" class="block-label pull-right shift_btn" <?= $_GET['type'] != 'shift' ? 'style="display:none;"' : '' ?>>Export Shifts</a>
            <?php }
            if(strpos($shift_fields, ',import_button,') !== FALSE) { ?>
                <a href="" onclick='overlayIFrameSlider("<?= WEBSITE_URL ?>/Calendar/shifts.php?<?= http_build_query($page_query) ?>&shiftid=IMPORT"); return false;' class="block-label pull-right shift_btn" <?= $_GET['type'] != 'shift' ? 'style="display:none;"' : '' ?>>Import Shifts</a>
            <?php }
        }
        if($use_shifts !== '' && $edit_access == 1 && $_GET['mode'] != 'staff_summary' && $_GET['mode'] != 'ticket_summary' && $_GET['mode'] != 'client') {
            if(!empty($_GET['shiftid'])) {
                unset($page_query['shiftid']);
            } else {
                $page_query['shiftid'] = 'NEW';
            }
            unset($page_query['equipment_assignmentid']);
            unset($page_query['offline']);
            unset($page_query['unbooked']);
            unset($page_query['teamid']);
            unset($page_query['subtab']);
            unset($page_query['action']);
            unset($page_query['bookingid']);
            unset($page_query['appoint_date']);
            unset($page_query['end_appoint_date']);
            unset($page_query['therapistsid']);
            unset($page_query['equipmentid']);
            unset($page_query['add_reminder']);
            ?><a href="" onclick='overlayIFrameSlider("<?= WEBSITE_URL ?>/Calendar/shifts.php?<?= http_build_query($page_query) ?>"); return false;' class="block-label pull-right shift_btn" <?= $_GET['type'] != 'shift' ? 'style="display:none;"' : '' ?>>New Shift</a><a href="" onclick="loadShiftView(); return false;" class="shift_anchor block-label pull-right" <?= $_GET['type'] == 'shift' ? 'style="display:none;"' : '' ?>>Shifts</a><?php
            $page_query['offline'] = $_GET['offline'];
            $page_query['unbooked'] = $_GET['unbooked'];
            $page_query['teamid'] = $_GET['teamid'];
            $page_query['subtab'] = $_GET['subtab'];
            $page_query['equipment_assignmentid'] = $_GET['equip_assignmentid'];
            $page_query['shiftid'] = $_GET['shiftid'];
            $page_query['add_reminder'] = $_GET['add_reminder'];
        } ?>
        <?php
        if($offline_mode !== '' && $edit_access == 1 && $_GET['mode'] != 'staff_summary' && $_GET['mode'] != 'ticket_summary') {
            unset($page_query['equipment_assignmentid']);
            if(!empty($_GET['offline'])) {
                unset($page_query['offline']);
            } else {
            	$page_query['offline'] = 1;
            }
            unset($page_query['unbooked']);
            unset($page_query['teamid']);
            unset($page_query['subtab']);
            unset($page_query['shiftid']);
            unset($page_query['action']);
            unset($page_query['bookingid']);
            unset($page_query['appoint_date']);
            unset($page_query['end_appoint_date']);
            unset($page_query['therapistsid']);
            unset($page_query['equipmentid']);
            unset($page_query['add_reminder']);
            ?><a href="?<?= http_build_query($page_query) ?>" class="block-label pull-right <?= $_GET['offline'] ? 'active' : '' ?>" <?= $_GET['offline'] ? 'style="background-color: red;"' : '' ?>>Offline Editing</a><?php
            if($_GET['offline']) { ?>
                <img src="../img/icons/ROOK-alert-icon.png" class="inline-img pull-right no-toggle" title="Click here to finish editing offline and send notifications to users of changes that have been made.">
            <?php }
            $page_query['offline'] = $_GET['offline'];
            $page_query['unbooked'] = $_GET['unbooked'];
            $page_query['teamid'] = $_GET['teamid'];
            $page_query['subtab'] = $_GET['subtab'];
            $page_query['equipment_assignmentid'] = $_GET['equip_assignmentid'];
            $page_query['shiftid'] = $_GET['shiftid'];
            $page_query['add_reminder'] = $_GET['add_reminder'];
        } ?>
        <?php
        if($all_tickets_button !== '' && $edit_access == 1 && $_GET['view'] != 'monthly' && $_GET['mode'] != 'staff_summary' && $_GET['mode'] != 'ticket_summary' && $_GET['mode'] != 'client') {
            unset($page_query['equipment_assignmentid']);
            unset($page_query['offline']);
            unset($page_query['teamid']);
            unset($page_query['subtab']);
            unset($page_query['shiftid']);
            unset($page_query['action']);
            unset($page_query['bookingid']);
            unset($page_query['appoint_date']);
            unset($page_query['end_appoint_date']);
            unset($page_query['therapistsid']);
            unset($page_query['equipmentid']);
            unset($page_query['add_reminder']);
            $page_query['load_all'] = 1;
            if(empty($_GET['unbooked'])) {
                $page_query['unbooked'] = 1;
            } else {
                unset($page_query['unbooked']);
            }
            ?><a href="" onclick="loadUnbookedList(this); return false;" data-type="" data-href="?<?= http_build_query($page_query) ?>" class="all_tickets_anchor block-label pull-right <?= $_GET['unbooked'] ? 'active' : '' ?>">All <?= TICKET_TILE ?></a><?php
            $page_query['unbooked'] = $_GET['unbooked'];
            $page_query['offline'] = $_GET['offline'];
            $page_query['teamid'] = $_GET['teamid'];
            $page_query['subtab'] = $_GET['subtab'];
            $page_query['equipment_assignmentid'] = $_GET['equip_assignmentid'];
            $page_query['shiftid'] = $_GET['shiftid'];
            $page_query['add_reminder'] = $_GET['add_reminder'];
        } ?>
        <?php
        if($use_unbooked !== '' && $edit_access == 1 && $_GET['mode'] != 'staff_summary' && $_GET['mode'] != 'ticket_summary' && $_GET['mode'] != 'client') {
            unset($page_query['equipment_assignmentid']);
            unset($page_query['offline']);
            unset($page_query['teamid']);
            unset($page_query['subtab']);
            unset($page_query['shiftid']);
            unset($page_query['action']);
            unset($page_query['bookingid']);
            unset($page_query['appoint_date']);
            unset($page_query['end_appoint_date']);
            unset($page_query['therapistsid']);
            unset($page_query['equipmentid']);
            unset($page_query['add_reminder']);
            $page_query['load_all'] = 0;
            if($_GET['type'] == 'uni' || $_GET['type'] == 'my') {
                if(strpos(','.$wait_list.',', ',ticket,') !== FALSE) {
                    if(empty($_GET['unbooked']) || $_GET['booking_type'] == 'appt' ) {
                        $page_query['unbooked'] = 1;
                    } else {
                        unset($page_query['unbooked']);
                    }
                    ?><a href="" onclick="loadUnbookedList(this); return false;" data-type="ticket" data-href="?<?= http_build_query($page_query) ?>&booking_type=ticket" class="unbooked_anchor block-label pull-right <?= $_GET['unbooked'] && $_GET['booking_type'] == 'ticket' ? 'active' : '' ?>">Unbooked Tickets</a><?php
                }
                if(strpos(','.$wait_list.',', ',ticket,') !== FALSE) {
                    if(empty($_GET['unbooked']) || $_GET['booking_type'] == 'ticket' ) {
                        $page_query['unbooked'] = 1;
                    } else {
                        unset($page_query['unbooked']);
                    }
                    ?><a href="" onclick="loadUnbookedList(this); return false;" data-type="appt" data-href="?<?= http_build_query($page_query) ?>&booking_type=appt" class="unbooked_anchor block-label pull-right <?= $_GET['unbooked'] && $_GET['booking_type'] == 'appt' ? 'active' : '' ?>">Unbooked Appointments</a><?php
                }
            } else {
                if(empty($_GET['unbooked'])) {
                    $page_query['unbooked'] = 1;
                } else {
                    unset($page_query['unbooked']);
                }
                ?><a href="" onclick="loadUnbookedList(this); return false;" data-type="" data-href="?<?= http_build_query($page_query) ?>" class="unbooked_anchor block-label pull-right <?= $_GET['unbooked'] ? 'active' : '' ?>">Unbooked List</a><?php
            }
            $page_query['unbooked'] = $_GET['unbooked'];
            $page_query['offline'] = $_GET['offline'];
            $page_query['teamid'] = $_GET['teamid'];
            $page_query['subtab'] = $_GET['subtab'];
            $page_query['equipment_assignmentid'] = $_GET['equip_assignmentid'];
            $page_query['shiftid'] = $_GET['shiftid'];
            $page_query['add_reminder'] = $_GET['add_reminder'];
        } ?>
        <?php
        if($add_reminder !== '' && $edit_access == 1) { ?>
            <a href='' onclick='overlayIFrameSlider("<?= WEBSITE_URL ?>/Calendar/add_reminder.php?region=<?= $_GET['region'] ?>&date=<?= $calendar_start ?>"); return false;' class="block-label pull-right">Reminders</a><?php
        } ?>
        <?php
        if($teams !== '' && $edit_access == 1) { ?>
            <a href='' onclick='overlayIFrameSlider("<?= WEBSITE_URL ?>/Calendar/teams.php?teamid=NEW&region=<?= $_GET['region'] ?>"); return false;' class="block-label pull-right">Teams</a><?php
        } ?>
        <?php
        if($equipment_assignment !== '' && $edit_access == 1) {
            $equipment_category = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['equipment_category'];
            $equipment_categories = array_filter(explode(',', $equipment_category));
            if(empty($equipment_categories) || count($equipment_categories) > 1) {
                $equipment_category = 'Equipment';
            }
            if (!empty($equipment_category)) { ?>
                <a href='' onclick='overlayIFrameSlider("<?= WEBSITE_URL ?>/Calendar/equip_assign.php?equipment_assignmentid=NEW&region=<?= $_GET['region'] ?>"); return false;' class="block-label pull-right"><?= $equipment_category ?> Assignment</a><?php
            }
        } ?>
        <?php
        if($wait_list == 'ticket' && $use_shift_tickets !== '' && $edit_access == 1) { ?>
            <a href='' onclick='overlayIFrameSlider("<?= WEBSITE_URL ?>/Calendar/tickets_shift.php?region=<?= $_GET['region'] ?>&weekly_days=<?= implode(',',$weekly_days) ?>"); return false;' class="block-label pull-right">Shift <?= TICKET_TILE ?></a><?php
        } ?>
        <?php
        if($use_shifts !== '' && $shift_conflicts_button == 1 && $edit_access == 1 && $_GET['type'] != 'my') {
            $shift_conflicts_last_checked = get_config($dbc, 'shift_conflicts_last_checked');
            $shifts_latest_updated = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`last_updated_time`) `latest_updated` FROM `contacts_shifts` WHERE `deleted` = 0"))['latest_updated'];
            if(strtotime($shifts_latest_updated) > strtotime($shift_conflicts_last_checked)) {
                $from_date = date('Y-m-d');
                $to_date = date('Y-m-d', strtotime($from_date.' + 1 month'));
                $shift_conflicts_check_num = get_config($dbc, 'shift_conflicts_check_num');
                $shift_conflicts_check_type = get_config($dbc, 'shift_conflicts_check_type');
                if($shift_conflicts_check_num > 0 && in_array($shift_conflicts_check_type, ['months','weeks'])) {
                    $to_date = date('Y-m-d', strtotime($from_date.' + '.$shift_conflicts_check_num.' '.$shift_conflicts_check_type));
                }
                $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query),MYSQLI_ASSOC));
                foreach($contact_list as $contact_id) {
                    $conflicts = [];
                    $has_conflict = 0;
                    for($current_date = $from_date; strtotime($current_date) <= strtotime($to_date); $current_date = date('Y-m-d', strtotime($current_date.' + 1 day'))) {
                        $current_conflicts = getShiftConflicts($dbc, $contact_id, $current_date);
                        if(!empty($current_conflicts)) {
                            $has_conflict = 1;
                            break;
                        }
                    }
                    if($has_conflict == 1) {
                        break;
                    }
                }
                set_config($dbc, 'shift_conflicts_last_checked', date('Y-m-d H:i:s'));
                set_config($dbc, 'shift_conflicts_has_conflict', $has_conflict);
            } else {
                $has_conflict = get_config($dbc, 'shift_conflicts_has_conflict');
            }
            if($has_conflict == 1) {
                $red_button = ' style="background-color: #f00;"';
            }
            $page_query['view'] = 'conflicts';
            if(empty($page_query['previous_view'])) {
                $page_query['previous_view'] = $_GET['view'];
            }
            if($_GET['view'] == 'conflicts') {
                $page_query['view'] = $_GET['previous_view'];
            }
            unset($page_query['shiftid']); ?>
            <a href='?<?= http_build_query($page_query) ?>' class="block-label pull-right <?= $_GET['view'] == 'conflicts' ? 'active' : '' ?>" <?= $red_button ?>>Shift Conflicts</a>
            <?php $page_query['view'] = $_GET['view'];
            $page_query['shiftid'] = $_GET['shiftid'];
        }
        ?>
        <?php if(get_config($dbc, 'calendar_online_users') == 1) { ?>
            <div class="pull-right">
                <div class="online-users">
                    <img src="<?= WEBSITE_URL ?>/img/person.PNG" class="online-users-icon theme-color-icon"><span class="online-users-count">0</span>
                    <div class="block-button online-users-block" style="display: none; width: 20em; position: absolute; top: 2em; left: 1.5em; z-index: 99999999999;"></div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>