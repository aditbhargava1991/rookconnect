<?php
$result = mysqli_query($dbc,"SELECT * FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `contactid` = '$contact_id'".$region_query);

while($row = mysqli_fetch_array( $result )) {
    $contactid = $row['contactid'];
    $staff = get_staff($dbc, $contactid);
    if(empty($row['calendar_color'])) {
    	$row['calendar_color'] = '#6DCFF6';
    }

    $query_next_actions = "SELECT `ea`.* FROM `estimate_actions` AS `ea` JOIN `estimate` AS `e` ON (`ea`.`estimateid`=`e`.`estimateid`) WHERE FIND_IN_SET ('$contactid', `e`.`assign_staffid`) AND `e`.`deleted`=0 AND FIND_IN_SET('$contactid', `ea`.`contactid`) AND `ea`.`deleted`=0 AND `ea`.`due_date`='". date('Y-m-d', strtotime($new_today_date)) ."'";
    $next_actions = mysqli_fetch_all(mysqli_query($dbc, $query_next_actions),MYSQLI_ASSOC);

    $num_rows = mysqli_num_rows($next_actions);

    if(!empty($next_actions)) {
    	$column .= '<div class="calendar_block calendarSortable" data-blocktype="'.$_GET['block_type'].'" data-contact="'.$contactid.'" data-date="'.$new_today_date.'">';
        $column .= '<h4>'.$staff.'</h4>';
        foreach ($next_actions as $row_action) {
            $estimate_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `estimate_name` FROM `estimate` WHERE `estimateid`='".$row_action['estimateid']."'"));
			$column .= '<a class="sortable-blocks" href="" onclick="'.($edit_access == 1 ? 'overlayIFrameSlider(\''.WEBSITE_URL.'/Estimate/estimates.php?view='.$row_action['estimateid'].'\');' : '').'return false;" style="display:block; margin: 0.5em; padding:5px; color:black; border-radius: 10px; background-color:'.$row['calendar_color'].';" data-estimate="'.$row_action['estimateid'].'" data-estimateaction="'.$row_action['id'].'" data-currentdate="'.$new_today_date.'" data-currentcontact="'.$staff.'" data-itemtype="estimate">';
			$column .= 'Follow Up: '.$estimate_name['estimate_name'];
			$column .= '</a>';
        }
        $column .= '</div>';
    }
}
?>