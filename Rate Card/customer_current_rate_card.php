<?php
/*
Rate Card Tiles
*/

?>
<script type="text/javascript">
$(document).ready(function() {
    $(".show_hide_ratecard").click( function() {
        var rid = this.id;
        var ratecardid = rid.split('_');
        var check_uncheck = 0;
        if($(this).is(':checked')) {
            var check_uncheck = 1;
        }
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "ratecard_ajax_all.php?fill=rate_card_config&action=show_hide&id="+ratecardid[2]+"&value="+check_uncheck,
			dataType: "html",   //expect html to be returned
			success: function(response){
                if(check_uncheck == 0) {
				   // alert('Rate Card hidden from view.');
                } else {
				    //alert('Rate Card Visible in view.');
                }
			}
		});
    });

    $(".archive_ratecard").click( function() {
        var rid = this.id;
        var ratecardid = rid.split('_');
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "ratecard_ajax_all.php?fill=rate_card_config&action=archive&id="+ratecardid[2],
			dataType: "html",   //expect html to be returned
			success: function(response){
			    //alert('Rate Card Archived.');
                location.reload();
			}
		});
    });

    $(".turn_on_off") // select the radio by its id
    .change(function(){ // bind a function to the change event
        if( $(this).is(":checked") ){ // check if the radio is checked
            var val = $(this).val(); // retrieve the value
            var rid = this.id;
            var ratecardid = rid.split('_');
            $.ajax({    //create an ajax request to load_page.php
                type: "GET",
                url: "ratecard_ajax_all.php?fill=rate_card_config&action=on_off&id="+ratecardid[2]+"&value="+val,
                dataType: "html",   //expect html to be returned
                success: function(response){
                    if(val == 0) {
                        //alert('Rate Card Turn Off');
                    } else {
                        //alert('Rate Card Turn On');
                    }
                    location.reload();
                }
            });
        }
    });

});

function selectCategory(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "ratecard_ajax_all.php?fill=rate_card_config&action=category&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}
</script>
		<div id='no-more-tables'>
        <?php
        $query_check_credentials = "SELECT r.*, c.`name` FROM `rate_card` r, `contacts` c WHERE r.`clientid` = c.`contactid` AND r.`deleted` = 0 ORDER BY `ratecardid` DESC";
        $result = mysqli_query($dbc, $query_check_credentials);

        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `field_config_ratecard`"));
        $db_config = ','.$get_field_config['dashboard_fields'].',';

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead>";
            echo '<tr class="hidden-xs hidden-sm">
                <th>Client</th>
                <th>Rate Card Name</th>'.
                (strpos($db_config, ',start_end_dates,') !== FALSE ? '<th>Start Date</th><th>End Date</th>' : '').
                (strpos($db_config, ',alert_date,') !== FALSE ? '<th>Alert Date</th>' : '').
                (strpos($db_config, ',alert_staff,') !== FALSE ? '<th>Alert Staff</th>' : '').
                (strpos($db_config, ',created_by,') !== FALSE ? '<th>Created By</th>' : '').
                '<th>Total Cost</th>';
            if(vuaed_visible_function($dbc, 'rate_card') == 1) {
                echo '<th>Turn On
                    <span class="popover-examples list-inline">&nbsp;
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turn on Rate Cards that you want to use to populate quotes and tickets"><img src="'.WEBSITE_URL.'/img/info.png" width="25"></a>
                    </span>
                </th>
                <th>Turn Off
                    <span class="popover-examples list-inline">&nbsp;
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turn off Rate Cards you no longer want visible"><img src="'.WEBSITE_URL.'/img/info.png" width="25"></a>
                    </span>
                </th>';
                echo '<th>Archive
                    <span class="popover-examples list-inline">&nbsp;
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Archived Rate Cards move to the archive."><img src="'.WEBSITE_URL.'/img/info.png" width="25"></a>
                    </span>
                </th>';
			}
                echo '<th>Function</th>
                <th>Last Edited</th>
                </tr></thead>';
        } else {
            echo "<h2>No Record Found.</h2>";
        }
        while($row = mysqli_fetch_array( $result )) {
            echo '<tr>';
            $ratecardid = $row['ratecardid'];
            echo '<td data-title="Client">' . decryptIt($row['name']) . '</td>';
            echo '<td data-title="Rate Card Name">' . $row['rate_card_name'] . '</td>';
            if(strpos($db_config, ',start_end_dates,') !== FALSE) {
                echo '<td data-title="Start Date">'.$row['start_date'].'</td>';
                echo '<td data-title="End Date">'.$row['end_date'].'</td>';
            }
            if(strpos($db_config, ',alert_date,') !== FALSE) {
                echo '<td data-title="Alert Date">'.$row['alert_date'].'</td>';
            }
            if(strpos($db_config, ',alert_staff,') !== FALSE) {
                echo '<td data-title="Alert Staff">';
                $staff_list = [];
                foreach(explode(',',$row['alert_staff']) as $staffid) {
                    if($staffid > 0) {
                        $staff_list[] = get_contact($dbc, $staffid);
                    }
                }
                echo implode(', ',$staff_list);
                echo '</td>';
            }
            if(strpos($db_config, ',created_by,') !== FALSE) {
                echo '<td data-title="Created By">'.get_contact($dbc, $row['created_by']).'</td>';
            }
            echo '<td data-title="Total Cost">$' . $row['total_price'] . '</td>';

            $sql=mysqli_query($dbc,"SELECT * FROM  `rate_card` WHERE `ratecardid` = '$ratecardid'");
            $on_security = '';

            while ($fieldinfo=mysqli_fetch_field($sql))
            {
                $field_name = $fieldinfo->name;
                $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `$field_name` FROM `rate_card` WHERE `$field_name` NOT LIKE '#**' OR `$field_name` IS NOT NULL"));
                if($get_config[$field_name]) {
                    $on_security[] = $field_name;
                }
            }

            $on = '';
            $off = '';
            if($row['on_off'] == 1) {
                $on = ' checked';
            } else {
                $off = ' checked';
            }
            if(vuaed_visible_function($dbc, 'rate_card') == 1) {
				echo '<td data-title="Turn On"><input '.$on.' type="radio" id="turn_onoff_'.$row['ratecardid'].'" name="turn_onoff_'.$row['ratecardid'].'" value="1" class="turn_on_off" style="height:20px;width:20px;"></td>';
				echo '<td data-title="Turn Off"><input '.$off.' type="radio" id="turn_onoff_'.$row['ratecardid'].'" name="turn_onoff_'.$row['ratecardid'].'" value="0" class="turn_on_off" style="height:20px;width:20px;"></td>';

				$hide = '';
				if($row['hide'] == 1) {
					$hide = ' checked';
				}

				echo '<td data-title="Archive"><input type="checkbox" value="Archive" style="height: 20px; width: 20px;" class="archive_ratecard"  id="archive_ratecard_'.$row['ratecardid'].'" name="archive_ratecard"></td>';
			}
            echo '<td data-title="Functions">';
            if(vuaed_visible_function($dbc, 'rate_card') == 1) {
				echo '<a href=\'?card=customer&type=customer&status=add&ratecardid='.$row['ratecardid'].'\'>Edit</a>';
            } else {
				echo '<a href=\'?card=customer&type=customer&status=show&ratecardid='.$row['ratecardid'].'\'>View</a>';
			}
            echo '</td>';

            $who_added = get_staff($dbc, $row['who_added']);
            echo '<td data-title="Last Edited">' . $who_added .' Edited On '. $row['when_added']. '</td>';
            echo '</tr>';

            echo "</tr>";
        }

        echo '</table>';
        ?>
		</div>