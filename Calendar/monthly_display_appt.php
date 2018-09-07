<?php
$result = mysqli_query($dbc,"SELECT * FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `contactid` = '$contact_id'".$region_query);

$old_staff = '';
while($row = mysqli_fetch_array( $result )) {
    $contactid = $row['contactid'];
    $staff = get_staff($dbc, $contactid);
    if(empty($row['calendar_color'])) {
    	$row['calendar_color'] = '#6DCFF6';
    }

    $all_booking_sql = "SELECT * FROM `booking` WHERE CONCAT('*#*',`therapistsid`,'*#*',`patientid`,'*#*') LIKE '%*#*$contactid*#*%' AND `follow_up_call_status` NOT LIKE '%cancel%' AND ((`appoint_date` BETWEEN '".$new_today_date." 00:00:00' AND '".$new_today_date." 11:59:59') OR (`end_appoint_date` BETWEEN '".$new_today_date." 00:00:00' AND '".$new_today_date." 11:59:59')) AND `deleted` = 0";
    $appointments = mysqli_fetch_all(mysqli_query($dbc, $all_booking_sql),MYSQLI_ASSOC);

    $num_rows = mysqli_num_rows($appointments);

    $j = 0;
    if(!empty($appointments)) {
    	$column .= '<div class="calendar_block calendarSortable" data-blocktype="'.$_GET['block_type'].'" data-contact="'.$contactid.'" data-date="'.$new_today_date.'">';
        $column .= '<h4>'.$staff.'</h4>';
        foreach ($appointments as $row_appt) {
			$status_class = 'unconfirmed';
			switch($row_appt['follow_up_call_status']) {
				case 'Booking Confirmed':
					$status_class = 'confirmed';
					break;
				case 'Arrived':
					$status_class = 'arrived';
					break;
				case 'Invoiced':
					$status_class = 'invoiced';
					break;
				case 'Paid':
					$status_class = 'paid';
					break;
				case 'Rescheduled':
					$status_class = 'rescheduled';
					break;
				case 'Late Cancellation / No-Show':
					$status_class = 'late_noshow';
					break;
				case 'Cancelled':
					$status_class = 'cancelled';
					break;
			}

			$page_query['action'] = 'view';
			$page_query['bookingid'] = $row_appt['bookingid'];
			$appt_page_query = $page_query;
			unset($appt_page_query['add_reminder']);
			unset($appt_page_query['unbooked']);
			unset($appt_page_query['equipment_assignmentid']);
			unset($appt_page_query['teamid']);
			$column .= '<a class="sortable-blocks '.$status_class.'" href="" onclick="'.($edit_access == 1 ? 'overlayIFrameSlider(\''.WEBSITE_URL.'/Calendar/booking.php?'.http_build_query($appt_page_query).'\');' : '').' return false;" style="display:block; margin: 0.5em; padding:5px; color:black; border-radius: 10px; background-color:'.$row['calendar_color'].';" data-appt="'.$row_appt['bookingid'].'" data-currentdate="'.$new_today_date.'" data-currentcontact="'.$staff.'" data-clientid="'.$row_appt['patientid'].'" data-itemtype="appt">';
			$column .= date('h:i a', strtotime($row_appt['appoint_date'])).' - '.date('h:i a', strtotime($row_appt['end_appoint_date'])).'<br>';
			$column .= get_contact($dbc, $row_appt['patientid']).'<br>';
			$column .= get_type_from_booking($dbc, $row_appt['type']).'<br>';
			$column .= $row_appt['follow_up_call_status'];
			$column .= '</a>';
			unset($page_query['action']);
			unset($page_query['bookingid']);
        }
        $column .= '</div>';
    }
}
?>