<?php
$client_type = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts`"))['contact_category'];
$result = mysqli_query($dbc,"SELECT * FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `contactid` = '$contact_id'".$region_query);
$lock_date = get_staff_schedule_lock_date($dbc);
$old_staff = '';
while($row = mysqli_fetch_array( $result )) {
    $contactid = $row['contactid'];
    $staff = get_staff($dbc, $contactid);
    if(empty($row['calendar_color'])) {
    	$row['calendar_color'] = '#3ac4f2';
    }

    if(get_config($dbc, 'shift_hide_if_day_off') == 1) {
        $daysoff = checkShiftIntervals($dbc, $contactid, $day_of_week, $new_today_date, 'daysoff');
        if(!empty($daysoff)) {
            $shifts = $daysoff;
        } else {
            $shifts = checkShiftIntervals($dbc, $contactid, $day_of_week, $new_today_date, 'shifts');
        }
    } else {
    	$shifts = checkShiftIntervals($dbc, $contactid, $day_of_week, $new_today_date, 'all');
    }

    $num_rows = mysqli_num_rows($shifts);

    $j = 0;
    if(!empty($shifts)) {
        $all_conflicts = getShiftConflicts($dbc, $contactid, $new_today_date);
        $shift_conflicts = [];
        foreach($all_conflicts as $conflict) {
            $shift_conflicts = array_merge(explode('*#*',$conflict), $shift_conflicts);
        }

    	$column .= '<div class="calendar_block calendarSortable" data-blocktype="'.$_GET['block_type'].'" data-contact="'.$contactid.'" data-date="'.$new_today_date.'">';
        $column .= '<h4>'.$staff.'</h4>';
        foreach ($shifts as $row_shifts) {
            if(in_array($row_shifts['shiftid'], $shift_conflicts)) {
                $has_conflict = true;
            } else {
                $has_conflict = false;
            }
            $calendar_color = '';
            if($shift_client_color == 1 && !empty($row_shifts['clientid'])) {
                $calendar_color = mysqli_fetch_array(mysqli_query($dbc, "SELECT `calendar_color` FROM `contacts` WHERE `contactid` = '".$row_shifts['clientid']."'"))['calendar_color'];
            }
            if(empty($calendar_color)) {
                $calendar_color = $row['calendar_color'];
            }
            $shift_bg_color = (!empty($_GET['shiftid']) ? ($_GET['shiftid'] == $row_shifts['shiftid'] ? '#3ac4f2' : '#ccc') : (!empty($row_shifts['dayoff_type']) ? '#ccc' : $calendar_color));
            $shift_fields = explode(',',mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts`"))['enabled_fields']);
            if(in_array('conflicts_highlight', $shift_fields) && $has_conflict) {
                $shift_bg_color = '#f00';
            }
            $warning_icon = '';
            if(in_array('conflicts_warning', $shift_fields) && $has_conflict) {
                $warning_icon = '<img title="This shift has a conflict with another shift." src="'.WEBSITE_URL.'/img/icons/yellow-warning.png" class="pull-right" style="max-height: 20px;">';
            }
			$page_query = $_GET;
			unset($page_query['shiftid']);
			unset($page_query['current_day']);
			$column .= ($row_shifts['startdate'] < $lock_date && !empty($lock_date) ? '<span class="sortable-blocks" ' : '<a class="sortable-blocks" ').($edit_access == 1 ? 'href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Calendar/shifts.php?'.http_build_query($page_query).'&shiftid='.$row_shifts['shiftid'].'&current_day='.$new_today_date.'\'); return false;"' : 'href="" onclick="return false;"').'style="display:block; margin: 0.5em; padding:5px; color:black; border-radius: 10px; background-color:'.$shift_bg_color.';" data-shift="'.$row_shifts['shiftid'].'" data-currentdate="'.$new_today_date.'" data-currentcontact="'.$staff.'" data-clientid="'.$row_shifts['clientid'].'" data-itemtype="shift">'.$warning_icon;
            if(!empty($row_shifts['heading'])) {
                $column .= $row_shifts['heading'].'<br />';
            }
			$column .= (!empty($row_shifts['dayoff_type']) ? 'Time Off: ' : 'Shift: ').date('g:i a', strtotime($row_shifts['starttime']))." - ".date('g:i a', strtotime($row_shifts['endtime'])).'<br />';
			if(!empty($row_shifts['clientid'])) {
				$column .= $client_type.': '.get_contact($dbc, $row_shifts['clientid']).'<br />';
			}
            if(!empty($row_shifts['notes'])) {
                $column .= 'Notes: '.html_entity_decode($row_shifts['notes']);
            }
			$column .= $row_shifts['startdate'] < $lock_date && !empty($lock_date) ? '</span>' : '</a>';
        }
        $column .= '</div>';
    }
}
?>