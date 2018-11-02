<?php
//include('../include.php');
//include 'config.php';
$value = $config['settings']['Choose Fields for Pay Period Dashboard'];
?>

<form id="form1" name="form1" method="get" enctype="multipart/form-data" class="form-horizontal" role="form"><?php
    if(vuaed_visible_function_custom($dbc)) { ?>
        <a class="btn brand-btn pull-right cursor-hand double-gap-bottom double-gap-top" onclick="overlayIFrameSlider('add_pay_period.php', '50%', false, false); return false;">Add Pay Period</a><?php
    } ?>

    <div id="no-more-tables"><?php    
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
        
        while($row = mysqli_fetch_array( $result )) {
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
                    if(vuaed_visible_function_custom($dbc)) { ?>
                        <a class="cursor-hand" onclick="overlayIFrameSlider('add_pay_period.php?pay_period_id=<?= $pay_period_id ?>', '50%', false, false); return false;">Edit</a> |
                        <a href="add_pay_period.php?action=delete&pay_period_id=<?= $pay_period_id ?>" onclick="return confirm('Are you sure you want to delete this Pay Period?')">Delete</a><?php
                    }
                echo '</td>';
            echo "</tr>";
        }

        echo '</table>'; ?>
    </div><?php
    
    if(vuaed_visible_function_custom($dbc)) { ?>
        <a class="btn brand-btn pull-right cursor-hand" onclick="overlayIFrameSlider('add_pay_period.php', '50%', false, false); return false;">Add Pay Period</a><?php
    } ?>
</form>