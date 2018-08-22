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
			<div class="col-sm-10"><h1>Funding Dashboard</h1></div>
			<div class="col-sm-2 double-gap-top">
				<?php
					if(config_visible_function($dbc, 'fund_development') == 1) {
						echo '<a href="field_config.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
					}
				?>
			</div>
			<div class="clearfix double-gap-bottom"></div>
			
        <form name="form_sites" method="post" action="" class="form-inline" role="form">
            
            <div class="tab-container">
                <div class="tab pull-left"><?php
                    if ( check_subtab_persmission( $dbc, 'fund_development', ROLE, 'funders' ) === true ) { ?>
                        <a href="funders.php"><button type="button" class="btn brand-btn mobile-block mobile-100" >Funders</button></a><?php
                    } else { ?>
                        <button type="button" class="btn disabled-btn mobile-block">Funders</button></a><?php
                    } ?>
                </div>
                <div class="tab pull-left"><?php
                    if ( check_subtab_persmission( $dbc, 'fund_development', ROLE, 'funding' ) === true ) { ?>
                        <a href="funding.php"><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab" >Funding</button></a><?php
                    } else { ?>
                        <button type="button" class="btn disabled-btn mobile-block">Funding</button></a><?php
                    } ?>
                </div>
            </div><!-- .tab-container -->
            <div class="clearfix"></div>
            
            <div id="no-more-tables">
                <a href="add_funding.php" class="btn brand-btn pull-right mobile-100-pull-right">Add Funding</a>
                <div class="clearfix"></div>
            <?php
            $enable_row_count = 0;
            $query_check_credentials = "SELECT * FROM fund_development_funding WHERE deleted=0;";

            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT fund_development_funding FROM field_config"));
                $value_config = ','.$get_field_config['fund_development_funding'].',';

                echo "<table class='table table-bordered table-striped'>";

                echo "<thead><tr class='hidden-xs hidden-sm'>";
                if (strpos($value_config, ','."Funding For".',') !== FALSE) {
                    echo '<th>Funding For</th>';
                    $enable_row_count++;
                }
                if (strpos($value_config, ','."Funding Date".',') !== FALSE) {
                    echo '<th>Funding Date</th>';
                    $enable_row_count++;
                }
                if (strpos($value_config, ','."Staff".',') !== FALSE) {
                    echo '<th>Staff</th>';
                    $enable_row_count++;
                }

                if (strpos($value_config, ','."Funding Heading".',') !== FALSE) {
                    echo '<th>Funding Heading</th>';
                    $enable_row_count++;
                }

                echo '<th>Type</th>';
                $enable_row_count++;
                
                if (strpos($value_config, ','."Receipt".',') !== FALSE) {
                    echo '<th>Receipt</th>';
                    $enable_row_count++;
                }
                if (strpos($value_config, ','."Description".',') !== FALSE) {
                    echo '<th>Description</th>';
                    $enable_row_count++;
                }
                if (strpos($value_config, ','."Day Funding".',') !== FALSE) {
                    echo '<th>Day Funding</th>';
                    $enable_row_count++;
                }

                //if (strpos($value_config, ','."Amount".',') !== FALSE) {
                    echo '<th>Amount</th>';
                //}
                //if (strpos($value_config, ','."GST".',') !== FALSE) {
                    echo '<th>GST</th>';
                //}
                //if (strpos($value_config, ','."Total".',') !== FALSE) {
                    echo '<th>Total</th>';
                //}
                //if (strpos($value_config, ','."Budget".',') !== FALSE) {
                    echo '<th>Budget</th>';
                //}
                echo '<th>Function</th>';
                echo "</tr></thead>";
            } else{
                echo "<div class='clearfix'><h2>No Record Found.</h2></div>";
            }
            $amount = 0;
            $gst = 0;
            $total = 0;
            $balance = 0;
            while($row = mysqli_fetch_array( $result ))
            {
               	echo "<tr>";

                if (strpos($value_config, ','."Funding For".',') !== FALSE) {
                    echo '<td data-title="Funding For">' . $row['funding_for'].'<br>'.$row['contact'] . '</td>';
                }
                if (strpos($value_config, ','."Funding Date".',') !== FALSE) {
                    echo '<td data-title="Funding Date">' . $row['ex_date'] . '</td>';
                }
                if (strpos($value_config, ','."Staff".',') !== FALSE) {
                    echo '<td data-title="Staff">' . $row['staff'] . '</td>';
                }
                if (strpos($value_config, ','."Funding Heading".',') !== FALSE) {
                    echo '<td data-title="Funding Heading">' . $row['title'] . '</td>';
                }
                echo '<td data-title="Type">' . $row['type'] . '</td>';
                if (strpos($value_config, ','."Receipt".',') !== FALSE) {
                    echo '<td data-title="Receipt"><a href="download/'.$row['ex_file'].'" target="_blank">' . $row['ex_file'] . '</a></td>';
                }
                if (strpos($value_config, ','."Description".',') !== FALSE) {
                    echo '<td data-title="Description">' . html_entity_decode($row['description']) . '</td>';
                }
                if (strpos($value_config, ','."Day Funding".',') !== FALSE) {
                    echo '<td data-title="Day Funding">' . $row['day_funding'] . '</td>';
                }
                //if (strpos($value_config, ','."Amount".',') !== FALSE) {
                    echo '<td data-title="Amount">$' . $row['amount'] . '</td>';
                //}
                //if (strpos($value_config, ','."GST".',') !== FALSE) {
                    echo '<td data-title="GST">$' . $row['gst'] . '</td>';
                //}
                //if (strpos($value_config, ','."Total".',') !== FALSE) {
                    echo '<td data-title="Total">$' . $row['total'] . '</td>';
                //}
                //if (strpos($value_config, ','."Budget".',') !== FALSE) {
                    echo '<td data-title="Budget">$' . $row['balance'] . '</td>';
                //}
                if(vuaed_visible_function($dbc, 'fund_development') == 1) {
				    echo '<td data-title="Function"><a href=\'add_funding.php?action=delete&fundingid='.$row['fundingid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a></td>';
                }

            	echo "</tr>";
                $amount += $row['amount'];
                $gst += $row['gst'];
                $total += $row['total'];
                $balance += $row['balance'];
            }

            echo "<tr>";
            echo '<td colspan="'.$enable_row_count.'" data-title="Final Total">Final Total</td>';
            echo '<td data-title="Amount">$' . number_format((float)$amount, 2, '.', '') . '</td>';
            echo '<td data-title="GST">$' . number_format((float)$gst, 2, '.', '') . '</td>';
            echo '<td data-title="Total">$' . number_format((float)$total, 2, '.', '') . '</td>';
            echo '<td data-title="Budget">$' . number_format((float)$balance, 2, '.', '') . '</td>';
            echo "</tr>";

            echo '</table></div>';
			echo '<a href="add_funding.php" class="btn brand-btn pull-right">Add Funding</a>';

            ?>
            </form>
        </div>
    </div>
</div>

<?php include ('../footer.php'); ?>