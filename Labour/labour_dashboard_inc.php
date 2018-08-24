<?php include_once('../include.php');
checkAuthorised('labour');
$search_query = '';
if(!empty($current_cat)) {
    $search_query .= " AND `labour_type` = '".$current_cat."'";
}
$rc_view_access = tile_visible($dbc, 'rate_card');
$rc_edit_access = vuaed_visible_function($dbc, 'rate_card');
$rc_subtab_access = check_subtab_persmission($dbc, 'rate_card', ROLE, 'labour'); ?>
<form name="form_sites" method="post" action="" class="form-horizontal" role="form">

    <div id="no-more-tables">
        <?php
        //Search
        if (isset($_POST['search_vendor_submit'])) {
            if (isset($_POST['search_vendor'])) {
                $vendor = $_POST['search_vendor'];
                $search_query .= " AND (`category` LIKE '%$vendor%' OR `heading` LIKE '%$vendor%')";
            }
        }


        /* Pagination Counting */
        $rowsPerPage = 25;
        $pageNum = 1;

        if(isset($_GET['page'])) {
            $pageNum = $_GET['page'];
        }

        $offset = ($pageNum - 1) * $rowsPerPage;

        $query_check_credentials = "SELECT * FROM labour WHERE deleted = 0 $search_query ORDER BY labour_type LIMIT $offset, $rowsPerPage";
        $query = "SELECT count(*) as numrows FROM labour WHERE deleted = 0 $search_query ORDER BY labour_type";

        $result = mysqli_query($dbc, $query_check_credentials);

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT labour_dashboard FROM field_config"));
            $value_config = ',Labour Type,Heading,'.$get_field_config['labour_dashboard'].',';

            // Added Pagination //
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            // Pagination Finish //

            echo "<table class='table table-bordered table-striped'>";
                echo "<thead>";
                    echo "<tr class='hidden-xs hidden-sm'>";
                    if (strpos($value_config, ','."Labour Code".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Labour Code')) {
                        echo '<th>Labour Code</th>';
                    }
                    if (strpos($value_config, ','."Labour Type".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Labour Type')) {
                        echo '<th>Labour Type</th>';
                    }
                    if (strpos($value_config, ','."Category".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Category')) {
                        echo '<th>Category</th>';
                    }
                    if (strpos($value_config, ','."Heading".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Heading')) {
                        echo '<th>Heading</th>';
                    }
                    if (strpos($value_config, ','."Name".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Name')) {
                        echo '<th>Name</th>';
                    }
                    // if (strpos($value_config, ','."Cost".',') !== FALSE) {
                    //     echo '<th>Cost</th>';
                    // }
                    if (strpos($value_config, ','."Description".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Description')) {
                        echo '<th>Description</th>';
                    }
                    if (strpos($value_config, ','."Quote Description".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Quote Description')) {
                        echo '<th>Quote Description</th>';
                    }
                    if (strpos($value_config, ','."Invoice Description".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Invoice Descr')) {
                        echo '<th>Invoice Description</th>';
                    }
                    if (strpos($value_config, ','."Ticket Description".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Ticket Description')) {
                        echo '<th>'.TICKET_NOUN.' Description</th>';
                    }
                    // if (strpos($value_config, ','."Daily Rate".',') !== FALSE) {
                    //     echo '<th>Daily Rate</th>';
                    // }
                    if (strpos($value_config, ','."WCB".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'WCB')) {
                        echo '<th>WCB</th>';
                    }
                    if (strpos($value_config, ','."Benefits".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Benefits')) {
                        echo '<th>Benefits</th>';
                    }
                    if (strpos($value_config, ','."Salary".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Salary')) {
                        echo '<th>Salary</th>';
                    }
                    if (strpos($value_config, ','."Bonus".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Bonus')) {
                        echo '<th>Bonus</th>';
                    }
                    if (strpos($value_config, ','."Minimum Billable".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Minimum Billable')) {
                        echo '<th>Minimum Billable</th>';
                    }
                    if (strpos($value_config, ','."Estimated Hours".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Estimated Hours')) {
                        echo '<th>Estimated Hours</th>';
                    }
                    if (strpos($value_config, ','."Actual Hours".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Actual Hours')) {
                        echo '<th>Actual Hours</th>';
                    }
                    if (strpos($value_config, ','."MSRP".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'MSRP')) {
                        echo '<th>MSRP</th>';
                    }
                    // if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) {
                    //     echo '<th>Hourly Rate</th>';
                    // }
                    if (strpos($value_config, ','."Rate Card Price".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Rate Card Price')) {
                        echo '<th>Rate Card Price</th>';
                    }
                    if (strpos($value_config, ','."Rate Card".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Rate Card') && $rc_view_access > 0 && $rc_subtab_access) {
                        echo '<th>Rate Card</th>';
                    }
                    if (vuaed_visible_function($dbc, 'labour') == 1) {
                        echo '<th>Function</th>';
                    }
                echo "</tr>";
            echo "</thead>";
        } else {
            echo "<h2>No Record Found.</h2>";
        }

        while($row = mysqli_fetch_array( $result ))
        {
            echo "<tr>";
            if (strpos($value_config, ','."Labour Code".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Labour Code')) {
                echo '<td data-title="Code">' . $row['labour_code'] . '</td>';
            }
            if (strpos($value_config, ','."Labour Type".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Labour Type')) {
   				echo '<td data-title="Type">' . $row['labour_type'] . '</td>';
            }
            if (strpos($value_config, ','."Category".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Category')) {
                echo '<td data-title="Category">' . $row['category'] . '</td>';
            }
            if (strpos($value_config, ','."Heading".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Heading')) {
                echo '<td data-title="Heading">' . $row['heading'] . '</td>';
            }
            if (strpos($value_config, ','."Name".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Name')) {
                echo '<td data-title="Name">' . $row['name'] . '</td>';
            }
            // if (strpos($value_config, ','."Cost".',') !== FALSE) {
            //     echo '<td data-title="Cost">' . $row['cost'] . '</td>';
            // }
            if (strpos($value_config, ','."Description".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Description')) {
                echo '<td data-title="Description">' . html_entity_decode($row['description']) . '</td>';
            }
            if (strpos($value_config, ','."Quote Description".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Quote Description')) {
                echo '<td data-title="Quote Desc">' . html_entity_decode($row['quote_description']) . '</td>';
            }
            if (strpos($value_config, ','."Invoice Description".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Invoice Description')) {
                echo '<td data-title="Invoice Desc">' . html_entity_decode($row['invoice_description']) . '</td>';
            }
            if (strpos($value_config, ','."Ticket Description".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Ticket Description')) {
                echo '<td data-title="'.TICKET_NOUN.' Desc">' . html_entity_decode($row['ticket_description']) . '</td>';
            }
            // if (strpos($value_config, ','."Daily Rate".',') !== FALSE) {
            //     echo '<td data-title="Daily Rate">' . $row['daily_rate'] . '</td>';
            // }
            if (strpos($value_config, ','."WCB".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'WCB')) {
                echo '<td data-title="WCB">' . $row['wcb'] . '</td>';
            }
            if (strpos($value_config, ','."Benefits".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Benefits')) {
                echo '<td data-title="Benefits">' . $row['benefits'] . '</td>';
            }
            if (strpos($value_config, ','."Salary".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Salary')) {
                echo '<td data-title="Salary">' . $row['salary'] . '</td>';
            }
            if (strpos($value_config, ','."Bonus".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Bonus')) {
                echo '<td data-title="Bonus">' . $row['bonus'] . '</td>';
            }
            if (strpos($value_config, ','."Minimum Billable".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Minimum Billable')) {
                echo '<td data-title="Min. Billable">' . $row['minimum_billable'] . '</td>';
            }
            if (strpos($value_config, ','."Estimated Hours".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Estimated Hours')) {
                echo '<td data-title="Est. Hours">' . $row['estimated_hours'] . '</td>';
            }
            if (strpos($value_config, ','."Actual Hours".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Actual Hours')) {
                echo '<td data-title="Actual Hours">' . $row['actual_hours'] . '</td>';
            }
            if (strpos($value_config, ','."MSRP".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'MSRP')) {
                echo '<td data-title="MSRP">' . $row['msrp'] . '</td>';
            }
            // if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) {
            //     echo '<td data-title="Hr. Rate">' . $row['hourly_rate'] . '</td>';
            // }
            if (strpos($value_config, ','."Rate Card Price".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Rate Card Price')) {
                echo '<td data-title="Rate Card Price">';
                $ratecards = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `tile_rate_card` WHERE `tile_name` = 'labour' AND `src_id` = '{$row['labourid']}' AND `deleted` = 0"),MYSQLI_ASSOC);
                foreach($ratecards as $ratecard) {
                    echo (!empty($ratecard['uom']) ? $ratecard['uom'].': ' : '').'$'.number_format($ratecard['price'], 2).'<br>';
                }
                echo '</td>';
            }
            if (strpos($value_config, ','."Rate Card".',') !== FALSE && check_dashboard_persmission($dbc, 'labour', ROLE, 'Rate Card') && $rc_view_access > 0 && $rc_subtab_access) {
                echo '<td data-title="Rate Card">';
                echo '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Labour/edit_rate_card.php?edit='.$row['labourid'].'&from_type=dashboard\', \'auto\', false, true); return false;">View'.($rc_edit_access > 0 && $rc_subtab_access ?'/Edit' : '').' Rate Card</a>';
                echo '</td>';
            }
            if (vuaed_visible_function($dbc, 'labour') == 1) {
                echo '<td data-title="Function">';
                echo '<a href=\'?edit='.$row['labourid'].'\'>Edit</a> | ';
				echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&labourid='.$row['labourid'].'&category='.$_GET['category'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
                echo '</td>';
            }

            echo "</tr>";
        }

        echo '</table>';
        // Added Pagination //
        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        // Pagination Finish //

        ?>
    </div>

</form>