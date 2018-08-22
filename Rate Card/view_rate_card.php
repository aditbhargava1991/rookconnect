<?php
/*
Rate CArd Tiles
*/
include ('../include.php');
checkAuthorised('rate_card');
error_reporting(0);

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


</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
	<div class="row">

        <h1>Rate Card Settings
        <?php
        if(config_visible_function($dbc, 'rate_card') == 1) {
            echo '<a href="field_config_rate_card.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
        }
        ?></h1>

        <a href='company_active_rate_card.php'><button type="button" class="btn brand-btn mobile-block" >My Companies Rate Card</button></a>
        <a href='active_rate_card.php'><button type="button" class="btn brand-btn mobile-block active_tab" >Customer Specific Rate Card</button></a>

        <br><br>

        <a href='active_rate_card.php'><button type="button" class="btn brand-btn mobile-block" >Active Rate Cards</button></a>
        <button type="button" class="btn brand-btn mobile-block active_tab" >Current Rate Card Status</button>
        <?php if(vuaed_visible_function($dbc, 'rate_card') == 1) { ?>
        <a href='add_rate_card.php'><button type="button" class="btn brand-btn mobile-block" >Add Rate Card</button></a>
        <?php } ?>

        <a href="<?php echo WEBSITE_URL; ?>/home.php"	class="btn brand-btn pull-right">Back</a>
		<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->

        <br>
		<div id='no-more-tables'>
        <?php
        $query_check_credentials = "SELECT r.*, c.name FROM rate_card r, contacts c WHERE r.clientid = c.contactid AND r.deleted = 0 ORDER BY ratecardid DESC";
        $result = mysqli_query($dbc, $query_check_credentials);

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            echo "<table class='table table-bordered table-striped'>";
            echo '<thead><tr class="hidden-xs hidden-sm">
                <th>Client</th>
                <th>Rate Card Name</th>
                <th>Total Cost</th>
                <th>Turn On
                    <span class="popover-examples list-inline">&nbsp;
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turn on Rate Cards that you want to use to populate quotes and tickets"><img src="'.WEBSITE_URL.'/img/info.png" width="25"></a>
                    </span>
                </th>
                <th>Turn Off
                    <span class="popover-examples list-inline">&nbsp;
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turn off Rate Cards you no longer want visible"><img src="'.WEBSITE_URL.'/img/info.png" width="25"></a>
                    </span>
                </th>';
                /*echo '<th>Hide
                    <span class="popover-examples list-inline">&nbsp;
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Hidding Rate Cards makes them only accissible to Administrators."><img src="'.WEBSITE_URL.'/img/info.png" width="25"></a>
                    </span>
                </th>';*/
                echo '<th>Archive
                    <span class="popover-examples list-inline">&nbsp;
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Archived Rate Cards move to the archive."><img src="'.WEBSITE_URL.'/img/info.png" width="25"></a>
                    </span>
                </th>
                <th>Edit</th>
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
            echo '<td data-title="Total Cost">$' . $row['total_price'] . '</td>';

            $sql=mysqli_query($dbc,"SELECT * FROM  rate_card WHERE ratecardid = '$ratecardid'");
            $on_security = '';

            while ($fieldinfo=mysqli_fetch_field($sql))
            {
                $field_name = $fieldinfo->name;
                $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM rate_card WHERE $field_name NOT LIKE '#**' OR $field_name IS NOT NULL"));
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

            echo '<td data-title="Turn On"><input '.$on.' type="radio" id="turn_onoff_'.$row['ratecardid'].'" name="turn_onoff_'.$row['ratecardid'].'" value="1" class="turn_on_off" style="height:20px;width:20px;"></td>';

            echo '<td data-title="Turn Off"><input '.$off.' type="radio" id="turn_onoff_'.$row['ratecardid'].'" name="turn_onoff_'.$row['ratecardid'].'" value="0" class="turn_on_off" style="height:20px;width:20px;"></td>';

            $hide = '';
            if($row['hide'] == 1) {
                $hide = ' checked';
            }
            //echo '<td data-title="Unit Number"><input type="checkbox" '.$hide.' value="Hide" style="height: 20px; width: 20px;" class="show_hide_ratecard"  id="showhide_ratecard_'.$row['ratecardid'].'" name="show_hide_ratecard"></td>';

            echo '<td data-title="Archive"><input type="checkbox" value="Archive" style="height: 20px; width: 20px;" class="archive_ratecard"  id="archive_ratecard_'.$row['ratecardid'].'" name="archive_ratecard"></td>';

            echo '<td data-title="Edit">';
            if(vuaed_visible_function($dbc, 'rate_card') == 1) {
            echo '<a href=\'add_rate_card.php?ratecardid='.$row['ratecardid'].'\'>Edit</a>';
            }
            echo '</td>';

            $who_added = get_staff($dbc, $row['who_added']);
            echo '<td data-title="Last Edited">' . $who_added .' Edited On '. $row['when_added']. '</td>';
            echo '</tr>';

            echo "</tr>";
        }

        echo '</table>';
        ?>
        <a href="<?php echo WEBSITE_URL; ?>/home.php"	class="btn brand-btn pull-right">Back</a>
		<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
		</div>
	</div>
</div>
<?php include ('../footer.php'); ?>