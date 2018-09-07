<?php
/*
Expenses Listing
*/
include ('../include.php');
checkAuthorised('fund_development');
?>

</head>
<body>
<?php include_once ('../navigation.php');
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
			<div class="col-sm-10"><h1>Fund Development Dashboard</h1></div>
			<div class="col-sm-2 double-gap-top">
				<?php
					if (config_visible_function($dbc, 'fund_development') == 1) {
						echo '<a href="field_config.php" class="mobile-block pull-right "><img style="width:50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
					}
				?>
			</div>
			<div class="clearfix double-gap-bottom"></div>
			
        <form name="form_sites" method="post" action="" class="form-inline" role="form">
            
            <div class="tab-container">
                <div class="tab pull-left"><?php
                    if ( check_subtab_persmission( $dbc, 'fund_development', ROLE, 'funders' ) === true ) { ?>
                        <a href="funders.php"><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab">Funders</button></a><?php
                    } else { ?>
                        <button type="button" class="btn disabled-btn mobile-block">Funders</button></a><?php
                    } ?>
                </div>
                <div class="tab pull-left"><?php
                    if ( check_subtab_persmission( $dbc, 'fund_development', ROLE, 'funding' ) === true ) { ?>
                        <a href="funding.php"><button type="button" class="btn brand-btn mobile-block mobile-100" >Funding</button></a><?php
                    } else { ?>
                        <button type="button" class="btn disabled-btn mobile-block">Funding</button></a><?php
                    } ?>
                </div>
            </div><!-- .tab-container -->
            <div class="clearfix"></div>
            
            <div id="no-more-tables">
                <a href="add_funders.php" class="btn brand-btn pull-right mobile-100-pull-right">Add Funders</a>
                <div class="clearfix"></div>
            <?php

            $query_check_credentials = "SELECT * FROM fund_development_funder";

            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {

                echo "<table class='table table-bordered table-striped'>";

                echo "<thead><tr class='hidden-xs hidden-sm'>";
                echo '<th>First Name</th>';
                echo '<th>Last Name</th>';
                echo '<th>Cell Phone</th>';
                echo '<th>Email Address</th>';
                /*
                if (strpos($value_config, ','."First Name".',') !== FALSE) {
                    echo '<th>First Name</th>';
                }
                if (strpos($value_config, ','."Last Name".',') !== FALSE) {
                    echo '<th>Last Name</th>';
                }
                if (strpos($value_config, ','."Client ID #".',') !== FALSE) {
                    echo '<th>Client ID #</th>';
                }

                if (strpos($value_config, ','."AISH #".',') !== FALSE) {
                    echo '<th>AISH #</th>';
                }
                if (strpos($value_config, ','."Work Phone".',') !== FALSE) {
                    echo '<th>Work Phone</th>';
                }
                if (strpos($value_config, ','."Home Phone".',') !== FALSE) {
                    echo '<th>Home Phone</th>';
                }
                if (strpos($value_config, ','."Cell Phone".',') !== FALSE) {
                    echo '<th>Cell Phone</th>';
                }

                if (strpos($value_config, ','."Fax #".',') !== FALSE) {
                    echo '<th>Fax #</th>';
                }
                if (strpos($value_config, ','."Email Address".',') !== FALSE) {
                    echo '<th>Email Address</th>';
                }
                if (strpos($value_config, ','."Address".',') !== FALSE) {
                    echo '<th>Address</th>';
                }
                if (strpos($value_config, ','."Postal/Zip Code".',') !== FALSE) {
                    echo '<th>Postal/Zip Code</th>';
                }
                if (strpos($value_config, ','."City/Town".',') !== FALSE) {
                    echo '<th>City/Town</th>';
                }
                if (strpos($value_config, ','."Province/State".',') !== FALSE) {
                    echo '<th>Province/State</th>';
                }
                if (strpos($value_config, ','."Country".',') !== FALSE) {
                    echo '<th>Country</th>';
                }
                */
                echo '<th>Active/Inactive</th>';
                echo "</tr></thead>";
            } else{
                echo "<div class='clearfix'><h2>No Record Found.</h2></div>";
            }
            
            while($row = mysqli_fetch_array( $result ))
            {
               	echo "<tr>";

                echo '<td data-title="Last Name">' . $row['first_name'] . '</td>';
                echo '<td data-title="Last Name">' . $row['last_name'] . '</td>';
                echo '<td data-title="Last Name">' . $row['cell_phone'] . '</td>';
                echo '<td data-title="Last Name">' . $row['email_address'] . '</td>';
                echo '<td>';
                    //if(vuaed_visible_function($dbc, 'medication') == 1) {
                        echo '<a href=\'add_funders.php?fundersid='.$row['fundersid'].'\'>Edit</a> | ';
                        echo '<a href=\'add_funders.php?action=delete&fundersid='.$row['fundersid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
                    //}
                echo '</td>';

                /*
                if (strpos($value_config, ','."First Name".',') !== FALSE) {
                    echo '<td data-title="First Name">' . $row['first_name'].'<br>'.$row['first_name'] . '</td>';
                }
                if (strpos($value_config, ','."Last Name".',') !== FALSE) {
                    echo '<td data-title="Last Name">' . $row['last_name'] . '</td>';
                }
                if (strpos($value_config, ','."Client ID #".',') !== FALSE) {
                    echo '<td data-title="Client ID #">' . $row['client_id'] . '</td>';
                }
                if (strpos($value_config, ','."AISH #".',') !== FALSE) {
                    echo '<td data-title="AISH #">' . $row['aish'] . '</td>';
                }
                if (strpos($value_config, ','."Work Phone".',') !== FALSE) {
                    echo '<td data-title="Work Phone">' . html_entity_decode($row['work_phone']) . '</td>';
                }
                if (strpos($value_config, ','."Cell Phone".',') !== FALSE) {
                    echo '<td data-title="Cell Phone">' . $row['cell_phone'] . '</td>';
                }
                if (strpos($value_config, ','."Fax #".',') !== FALSE) {
                    echo '<td data-title="Fax #">' . $row['fax'] . '</td>';
                }
                if (strpos($value_config, ','."Email Address".',') !== FALSE) {
                    echo '<td data-title="Email Address">' . $row['email_address'] . '</td>';
                }
                if (strpos($value_config, ','."Address".',') !== FALSE) {
                    echo '<td data-title="Address">' . $row['address'] . '</td>';
                }
                if (strpos($value_config, ','."Postal/Zip Code".',') !== FALSE) {
                    echo '<td data-title="Postal/Zip Code">' . $row['postal_zip_code'] . '</td>';
                }
                if (strpos($value_config, ','."City/Town".',') !== FALSE) {
                    echo '<td data-title="City/Town">' . $row['city_town'] . '</td>';
                }
                if (strpos($value_config, ','."Province/State".',') !== FALSE) {
                    echo '<td data-title="Province/State">' . $row['province_state'] . '</td>';
                }
                if (strpos($value_config, ','."Country".',') !== FALSE) {
                    echo '<td data-title="country">' . $row['country'] . '</td>';
                }
                if (strpos($value_config, ','."Support Documents".',') !== FALSE) {
                    echo '<td data-title="Support Documents">' . $row['support_documents'] . '</td>';
                }
                */
            	echo "</tr>";
            }

            echo '</table></div>';
            ?>
            </form>
        </div>
    </div>
</div>

<?php include ('../footer.php'); ?>