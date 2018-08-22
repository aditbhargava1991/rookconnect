<?php
include('../include.php');
?>
</head>
<body>
<?php 
include_once ('../navigation.php');
checkAuthorised('timesheet');
include 'config.php';

$value = $config['settings']['Choose Fields for Pay Period Dashboard'];

?>

<div class="container triple-pad-bottom">
    <div class="row">
        <div class="col-md-12">

        <h1 class="">Pay Period Dashboard
        <?php
        if(config_visible_function_custom($dbc)) {
            echo '<a href="field_config.php?from_url=pay_period.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
        }
        ?>
        </h1>

        <form id="form1" name="form1" method="get" enctype="multipart/form-data" class="form-horizontal" role="form">

        <?php echo get_tabs('Pay Period', '', array('db' => $dbc, 'field' => $value['config_field'])); ?>
        <br><br>


        <?php
        if(vuaed_visible_function_custom($dbc)) {
            echo '<a href="add_pay_period.php" class="btn brand-btn mobile-block pull-right">Add Pay Period</a>';
        }
        ?>
        <br><br><br>

            <div id="no-more-tables">

            <?php    

            $tb_field = $value['config_field'];

            $query_check_credentials = 'SELECT * FROM pay_period';

            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {

                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$tb_field." FROM field_config"));
                $value_config = ','.$get_field_config[$tb_field].',';

                echo "<table class='table table-bordered table-striped'>";
                echo "<thead><tr class='hidden-xs hidden-sm'>";

                foreach($value['data'] as $tab_name => $tabs) {
                    foreach($tabs as $field) {
                        if (strpos($value_config, ','.$field[2].',') !== FALSE) {
                            echo '<th>'.$field[0].'</th>';
                        }
                    }
                }
                    echo '<th>Function</th>';
                echo "</tr></thead>";
            } else {
                echo "<h2>No Record Found.</h2>";
            }
            while($row = mysqli_fetch_array( $result ))
            {
                echo "<tr>";
                $pay_period_id = $row['pay_period_id'];

                foreach($value['data'] as $tab_name => $tabs) {
                    foreach($tabs as $field) {
                        if (strpos($value_config, ','.$field[2].',') !== FALSE) {
                            echo '<td>';
                            if($field[2] == 'staff') {
								$staff_list = array_filter(explode(',',$row[$field[2]]));
								foreach($staff_list as $staff) {
									echo get_staff($dbc, $staff).'<br />';
								}
                            } else if($field[2] == 'all_staff') {
                                echo ($row[$field[2]] ? 'Yes' : 'No');    
                            } else {
                                echo $row[$field[2]];    
                            }
                            echo '</td>';
                        }
                    }
                }

                echo '<td>';
                if(vuaed_visible_function_custom($dbc)) {
                echo '<a href=\'add_pay_period.php?pay_period_id='.$pay_period_id.'\'>Edit</a> | ';
                echo '<a href=\'add_pay_period.php?action=delete&pay_period_id='.$pay_period_id.'\' onclick="return confirm(\'Are you sure?\')">Delete</a>';
                }
                echo '</td>';

                echo "</tr>";
            }

            echo '</table></div>';
            if(vuaed_visible_function_custom($dbc)) {
                echo '<a href="add_pay_period.php" class="btn brand-btn mobile-block pull-right">Add Pay Period</a>';
            }

            ?>
        </form>

        </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
