<?php
$result = mysqli_query($dbc,"SELECT * FROM `project` WHERE `deleted` = 0 AND `projectid` = '$contact_id'");

$old_staff = '';
while($row = mysqli_fetch_array( $result )) {
    $completed_tickets = 0;
    if(check_subtab_persmission($dbc, 'project', ROLE, $row['projecttype'])) {
    	$project_name = $row['project_name'];

        if(empty($row['calendar_color'])) {
        	$row['calendar_color'] = '#6DCFF6';
        }

    	$all_tickets_sql = "SELECT * FROM `tickets` WHERE `to_do_date` = '".$new_today_date."' AND `projectid` = '".$row['projectid']."' AND `deleted` = 0 AND `status` NOT IN ('Archive', 'Done')";
    	$tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);

        $num_rows = mysqli_num_rows($tickets);

        if(!empty($tickets)) {
        	$column .= '<div class="calendar_block calendarSortable" data-blocktype="'.$_GET['block_type'].'" data-contact="'.$row['projectid'].'" data-date="'.$new_today_date.'">';
            $column .= '<h4>'.$project_name.'</h4>';
            foreach ($tickets as $row_ticket) {
                $status = $row_ticket['status'];
                if($calendar_checkmark_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
                    $checkmark_ticket = 'calendar-checkmark-ticket-month';
                } else {
                    $checkmark_ticket = '';
                }
                if($calendar_ticket_color_code_tabs == 1 && !empty($ticket_tabs_color[$row_ticket['ticket_type']])) {
                    $ticket_styling = ' background-color:'.$ticket_tabs_color[$row_ticket['ticket_type']].';';
                } else if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
                    $ticket_styling = ' background-color:'.$calendar_completed_color[$status].';';
                } else if($calendar_highlight_incomplete_tickets == 1 && in_array($status, $calendar_incomplete_status)) {
                    $ticket_styling = ' background-color:'.$calendar_incomplete_color[$status].';';
                } else {
                    $ticket_styling = ' background-color:'.$row['calendar_color'].';';
                }
                if(in_array($status, $calendar_checkmark_status)) {
                    $completed_tickets++;
                }
                $status_icon = get_ticket_status_icon($dbc, $row_ticket['status']);
                if(!empty($status_icon)) {
                    $icon_img = '';
                    $icon_background = '';
                    if($calendar_ticket_status_icon == 'background') {
                        $icon_background = " background-image: url('".$status_icon."'); background-repeat: no-repeat; height: 100%; background-size: contain; background-position: center;";
                    } else {
                        if($status_icon == 'initials') {
                            $icon_img = '<span class="id-circle-small pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($row_ticket['status']).'</span>';
                        } else {
                            $icon_img = '<img src="'.$status_icon.'" class="pull-right" style="max-height: 20px;">';
                        }
                    }
                } else {
                    $icon_img = '';
                    $icon_background = '';
                }
    			$column .= "<a class='sortable-blocks ".$checkmark_ticket."' href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Ticket/preview_ticket.php?action=view&ticketid=".$row_ticket['ticketid']."\", false); return false;' style='display:block; margin: 0.5em; padding:5px; color:black; border-radius: 10px;".$ticket_styling.$icon_background.";' data-ticket='".$row_ticket['ticketid']."' data-itemtype='ticket_event'>".$icon_img;
                if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
                    $column .= '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
                }
    			$column .= date('h:i a', strtotime($row_ticket['member_start_time']))." - ".date('h:i a', strtotime($row_ticket['member_end_time']));
    			$column .= "</a>";
            }
            if($ticket_summary != '') {
                $column .= '<span>Completed '.$completed_tickets.' of '.count($tickets).' '.(count($tickets) == 1 ? TICKET_NOUN : TICKET_TILE).'</span>';
            }
            $column .= '</div>';
        }
    }
}
?>