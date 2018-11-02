<?php // Get the updated compensation details
$report_validation = mysqli_query($dbc,"SELECT 'ticket' `item_type`, `ticketid` `id`, `to_do_date` `date`, 1 `count`  FROM `tickets` WHERE `equipmentid`='$equipmentid' AND `deleted`=0 AND `to_do_date` BETWEEN '$starttime' AND '$endtime'
    UNION SELECT 'ticket_stop' `item_type`, `id`, `to_do_date` `date`, 1 `count` FROM `ticket_schedule` WHERE `equipmentid`='$equipmentid' AND `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `deleted`=0) AND `deleted`=0 AND `to_do_date` BETWEEN '$starttime' AND '$endtime'
    UNION SELECT 'ticket_attach' `item_type`, `id`, `date_stamp` `date`, 1 `count` FROM `ticket_attached` WHERE `src_table`='Equipment' AND `item_id`='$equipmentid' AND `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `deleted`=0) AND `deleted`=0 AND `date_stamp` BETWEEN '$starttime' AND '$endtime'
    UNION SELECT 'ticket_time' `item_type`, `id`, `date_stamp` `date`, `hours_set` `count` FROM `ticket_attached` WHERE `src_table`='Equipment' AND `item_id`='$equipmentid' AND `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `deleted`=0) AND `deleted`=0 AND `date_stamp` BETWEEN '$starttime' AND '$endtime'
    UNION SELECT 'km' `item_type`, `id`, `end` `date`, `mileage` `count` FROM `mileage` WHERE `equipmentid`='$equipmentid' AND `end` BETWEEN '$starttime' AND '$endtime 23:59:59'
    UNION SELECT 'log_km' `item_type`, `safetyinspectid` `id`, `inspect_date` `date`, `final_odo_kms` - `begin_odo_kms` `count` FROM `site_work_driving_inspect` WHERE `inspect_date` BETWEEN '$starttime' AND '$endtime' AND `drivinglogid` IN (SELECT `drivinglogid` FROM `site_work_driving_log` WHERE `equipment`='$equipmentid')");
if($report_validation->num_rows > 0) {
    $comp_entered = true;
    $equip_total = 0;
    $rate_card = $dbc->query("SELECT * FROM `rate_card` WHERE (`equipment` LIKE '%**$equipmentid#%' OR `equipment` LIKE '$equipmentid#%') AND `deleted`=0 AND `on_off`=1 AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) AND `start_date` < DATE(NOW()) ORDER BY `start_date` DESC");
    $rate_ticket = 0;
    if($rate_card->num_rows > 0) {
        $rate_card = explode('**',$rate_card->fetch_assoc()['equipment']);
        foreach($rate_card as $rate_line) {
            if($rate_line[0] == $equipmentid) {
                $rate_ticket = $rate_line[2];
            }
        }
    }
    if(!($rate_ticket > 0)) {
        $rate_ticket = $dbc->query("SELECT `rate_compensation`.`comp_fee` FROM `rate_compensation` LEFT JOIN `rate_card` ON `rate_compensation`.`rate_card`=`rate_card`.`ratecardid` WHERE `rate_card`.`clientid`='".$equip['ownership_status']."' AND `rate_compensation`.`item_type`='equip_ticket'")->fetch_assoc()['comp_fee'];
    }
    $rate_km = $dbc->query("SELECT `rate_compensation`.`comp_fee` FROM `rate_compensation` LEFT JOIN `rate_card` ON `rate_compensation`.`rate_card`=`rate_card`.`ratecardid` WHERE `rate_card`.`clientid`='".$equip['ownership_status']."' AND `rate_compensation`.`item_type`='equip_km'")->fetch_assoc()['comp_fee'];
    $rate_hour = $dbc->query("SELECT `rate_compensation`.`comp_fee` FROM `rate_compensation` LEFT JOIN `rate_card` ON `rate_compensation`.`rate_card`=`rate_card`.`ratecardid` WHERE `rate_card`.`clientid`='".$equip['ownership_status']."' AND `rate_compensation`.`item_type`='equip_hour'")->fetch_assoc()['comp_fee'];
    $report_data .= '<h4>'.implode(': ',array_filter([$equip['category'],$equip['make'],$equip['model'],$equip['unit_number'],$equip['label']])).' -  Compensation</h4>';
    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr nobr="true" style="'.$table_row_style.'">';
    $report_data .= '<th width="50%">Description</th>';
    $report_data .= '<th width="20%">Date</th>';
    $report_data .= '<th width="10%">Quantity</th>';
    $report_data .= '<th width="20%">Total Compensation</th>';
    while($report_row = $report_validation->fetch_assoc()) {
        $item = $report_row['id'];
        $total_fee = $report_row['count'] * (in_array($report_row['item_type'],['km','log_km']) ? $rate_km : (in_array($report_row['item_type'],['ticket_attach','ticket_stop','ticket']) ? $rate_ticket : (in_array($report_row['item_type'],['ticket_time']) ? $rate_hour : 0)));
        if($total_fee > 0) {
            $description = '';
            switch($report_row['item_type']) {
                case 'km':
                    $info = $dbc->query("SELECT * FROM `mileage` WHERE `id`='$item'")->fetch_assoc();
                    $description = implode(': ',array_filter(['Mileage',$info['category'],$info['details']]));
                    break;
                case 'log_km':
                    $info = $dbc->query("SELECT * FROM `site_work_driving_inspect` WHERE `safetyinspectid`='$item'")->fetch_assoc();
                    $description = implode(': ',array_filter(['Safety Driving Log KM',$info['location_of_presafety'],$info['location_of_postsafety']]));
                    break;
                case 'ticket_attach':
                    $info = $dbc->query("SELECT `tickets`.* FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_attached`.`id`='$item'")->fetch_assoc();
                    $description = get_ticket_label($dbc, $info);
                    break;
                case 'ticket_time':
                    $info = $dbc->query("SELECT `tickets`.* FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_attached`.`id`='$item'")->fetch_assoc();
                    $description = get_ticket_label($dbc, $info);
                    break;
                case 'ticket_stop':
                    $info = $dbc->query("SELECT `tickets`.*, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name` FROM `ticket_schedule` LEFT JOIN `tickets` ON `ticket_schedule`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_schedule`.`id`='$item'")->fetch_assoc();
                    $description = get_ticket_label($dbc, $info).': '.(empty($info['client_name']) ? $info['location_name'] : $info['client_name']);
                    break;
                case 'ticket':
                    $info = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='$item'")->fetch_assoc();
                    $description = get_ticket_label($dbc, $info);
                    break;
            }

            $report_data .= '<tr nobr="true">';
            $report_data .= '<td>'.$description.'</td>';
            $report_data .= '<td>'.$report_row['date'].'</td>';
            $report_data .= '<td>'.round($report_row['count'],4).'</td>';
            $report_data .= '<td>$'.number_format($total_fee, 2).'</td>';
            $report_data .= '</tr>';

            $equip_total += $total_fee;
        }
    }
    $report_data .= '<tr nobr="true" style="font-weight:bold;">';
    $report_data .= '<td colspan="3">Total Compensation</td>';
    $report_data .= '<td>$'.number_format($equip_total, 2).'</td>';
    $report_data .= '</tr>';
    $report_data .= '</table><br />';
} else {
    $report_data .= '<h4>'.implode(': ',array_filter([$equip['category'],$equip['make'],$equip['model'],$equip['unit_number'],$equip['label']])).' - No Compensation Found</h4>';
}