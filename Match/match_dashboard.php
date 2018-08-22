<?php include_once('../include.php');
checkAuthorised('match');

//Status change
if(isset($_GET['status'])) {
    $matchid = $_GET['matchid'];
    $status = $_GET['status'];
    if($status == 0) {
        $status = 'Active';
    } elseif($status == 1) {
        $status = 'Suspend';
    }

    $match = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `match_contact` WHERE `matchid` = '$matchid'"));
    $history = '';
    if ($status != $match['status']) {
        $history = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Status on '.date('Y-m-d H:i:s').'<br>';   
    }

    $query_update_status = "UPDATE match_contact SET status = '$status', `history` = CONCAT(IFNULL(history, ''), '$history') WHERE matchid = $matchid";
    $result_update_status = mysqli_query($dbc, $query_update_status);
}
/* Get logged in user's role */
$contacterid    = $_SESSION['contactid'];
if ( !empty ( $_SESSION[ 'role' ] ) ) {
    $level_url = $_SESSION[ 'role' ];

} else {
    $result = mysqli_query ( $dbc, "SELECT * FROM contacts WHERE contactid='$contacterid'" );

    while ( $row = mysqli_fetch_assoc( $result ) ) {
        $role = $row[ 'role' ];
    }

    $level_url = (strpos(','.ROLE.',',',super,') !== false) ? 'admin' : $role;
}
?>

<script type="text/javascript">
$(document).ready(function() {
    $('.iframe_open').click(function(){
        var id = $(this).attr('id');
        $('#iframe_instead_of_window').attr('src', 'match_history.php?matchid='+id);
        $('.iframe_title').text('Match History');
        $('.iframe_holder').show();
        $('.hide_on_iframe').hide();
    });

    $('.close_iframer').click(function(){
        $('.iframe_holder').hide();
        $('.hide_on_iframe').show();
    });
});
</script>

<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
    <ul>
        <form action="" method="GET">
            <li class="standard-sidebar-searchbox">
                <input type="text" name="search_query" class="form-control" placeholder="Search <?= $tab_title ?>" value="<?= $_GET['search_query'] ?>">
            </li>
        </form>
        <a href="?"><li class="active blue">Matches</li></a>
    </ul>
</div>

<div class="scale-to-fill has-main-screen">
    <div class="main-screen standard-body form-horizontal">
        <div class="standard-body-title">
            <h3>Matches</h3>
        </div>
        <div class="standard-body-content" style="padding: 1em;">
            <div class="notice double-gap-bottom popover-examples">
                <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
                <div class="col-sm-11"><span class="notice-name">NOTE:</span>
                The matching tile allows staff to be matched to specific contact/customer information for a set period of time. The match system allows staff to view and have full access to specific contacts/customer information based on the timeline set. Once the match expiry time has passed, staff will no longer have access to the matched contact/customer information.</div>
                <div class="clearfix"></div>
            </div>

    	    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

                <div id="no-more-tables">
                <span class="pull-right">
                    <img src="<?php echo WEBSITE_URL;?>/img/block/red.png" width="23" height="23" border="0" alt=""> Suspended
                    <img src="<?php echo WEBSITE_URL;?>/img/block/green.png" width="23" height="23" border="0" alt=""> Active
                </span><br><br>
                <?php

                $query_check_credentials = "SELECT * FROM match_contact WHERE deleted = 0";

                if(!empty($_GET['search_query'])) {
                    $search = mysqli_real_escape_string($dbc, $_GET['search_query']);
                    $id_list = search_contacts_table($dbc, $search);
                    $query_check_credentials .= " AND ";
                    foreach(explode(',',$id_list) as $id) {
                        $query_check_credentials .= "find_in_set($id, support_contact) <> 0 OR find_in_set($id, staff_contact) <> 0 OR ";
                    }
                    $query_check_credentials = rtrim($query_check_credentials, " OR");
                }

                if ($edit_access == 0) {
                    $query_check_credentials .= " AND find_in_set($contacterid, support_contact) <> 0 OR find_in_set($contacterid, staff_contact) <> 0";
                }

                $result = mysqli_query($dbc, $query_check_credentials);

                $num_rows = mysqli_num_rows($result);
                if($num_rows > 0) {
                    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT medication_dashboard FROM field_config"));
                    $value_config = ','.$get_field_config['medication_dashboard'].',';

                    echo "<table class='table table-bordered table-striped'>";
                    echo "<thead><tr class='hidden-xs hidden-sm'>";
                        echo '<th>Staff</th>';
                        echo '<th>Contacts</th>';
                        echo '<th>Timeline</th>';
                        echo '<th>Follow Up</th>';
                        echo '<th>End Date</th>';
                        echo '<th>Status</th>';
                        echo '<th>History</th>';
                        echo '<th>Function</th>';
                        echo "</tr></thead>";
                } else {
                    echo "<h2>No Record Found.</h2>";
                }

                $curr_date = date("Y-m-d");

                while($row = mysqli_fetch_array( $result ))
                {
                    if(!empty($row['end_date']) && $curr_date > $row['end_date'] && $row['status'] != 'Suspend') {
                        $row['status'] = 'Suspend';
                        $history = 'Match passed end date and was automatically suspended on '.date('Y-m-d H:i:s').'<br>';
                        $query_update_status = "UPDATE match_contact SET status = 'Suspend', history = CONCAT(IFNULL(history, ''), '$history') WHERE matchid = ". $row['matchid'];
                        $result_update_status = mysqli_query($dbc, $query_update_status);
                    }

                    $style = '';
                    if($row['status'] == 'Active') {
                        $style = 'style="color: green;"';
                    }
                    if($row['status'] == 'Suspend') {
                        $style = 'style="color: red;"';
                    }
                    echo "<tr $style>";
                    $matchid = $row['matchid'];

                    $staff_contacts_arr = explode(',', $row['staff_contact']);
                    $staff_contacts = [];
                    foreach($staff_contacts_arr as $value){
                        array_push($staff_contacts, !empty(get_client($dbc, $value)) ? get_client($dbc, $value) : get_contact($dbc, $value));
                    }
                    echo '<td data-title="Staff">' . implode(', ', $staff_contacts) . '</td>';

                    $support_contacts_arr = explode(',', $row['support_contact']);
                    $support_contacts = [];
                    foreach($support_contacts_arr as $value){
                        array_push($support_contacts, !empty(get_client($dbc, $value)) ? get_client($dbc, $value) : get_contact($dbc, $value));
                    }
                    echo '<td data-title="Contacts">' . implode(', ', $support_contacts) . '</td>';

                    echo '<td data-title="Timeline">'. $row['match_date']. '</td>';
                    echo '<td data-title="Follow Up">'. $row['follow_up_date']. '</td>';
                    echo '<td data-title="End Date">'. $row['end_date']. '</td>';

                    if($row['status'] == 'Active') {
                        echo '<td data-title="Status">Active | <a href="?matchid='. $row['matchid']. '&status=1">Suspend</a></td>';
                    } else if($row['status'] == 'Suspend') {
                        echo '<td data-title="Status"><a href="?matchid='. $row['matchid']. '&status=0">Active</a> | Suspend</td>';
                    } else {
                        echo '<td data-title="Status"><a href="?matchid='. $row['matchid']. '&status=0">Active</a> | <a href="?matchid='. $row['matchid']. '&status=1">Suspend</a></td>';
                    }

                    echo '<td data-title="History">';
                    echo '<span class="iframe_open" id="'.$row['matchid'].'"style="cursor:pointer">View All</span></td>';

                    echo '<td data-title="Function">';
                    if(vuaed_visible_function($dbc, 'match') == 1) {
                    echo '<a href=\'?edit='.$matchid.'\'>Edit</a> | ';
    				echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&matchid='.$matchid.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
                    }
                    echo '</td>';

                    echo "</tr>";
                }

                echo '</table></div>';

                ?>
            </form>
        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
