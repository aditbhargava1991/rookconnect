<?php
include ('../include.php');
include 'config.php';

$value = $config['settings']['Choose Fields for Pay Period'];

if(isset($_GET['action']) && $_GET['action'] == 'delete') {
    mysqli_query($dbc, "DELETE FROM pay_period WHERE pay_period_id=".$_GET['pay_period_id']);

    echo '<script type="text/javascript"> window.location.replace("pay_period.php"); </script>';
}

if (isset($_POST['submit'])) {

    $inputs = get_post_inputs($value['data']);
    $files = get_post_uploads($value['data']);
    move_files($files);

    if(empty($_POST['pay_period_id'])) {
        $query_insert_vendor = prepare_insert($inputs, 'pay_period');
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $bowel_movement_id = mysqli_insert_id($dbc);
        $url = 'Added';
    } else {
        $pay_period_id = $_POST['pay_period_id'];
        $query_update_vendor = prepare_update($inputs, 'pay_period', 'pay_period_id', $pay_period_id);
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $url = 'Updated';
    }

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }

    echo '<script type="text/javascript"> window.location.replace("pay_period.php"); </script>';

}
?>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('timesheet');
?>
<div class="container">
  <div class="row">

    <h1 class="triple-pad-bottom">Pay Period</h1>

    <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php

        $inputs = get_all_inputs($value['data']);

        foreach($inputs as $input) {
            $$input = '';
        }

        if(!empty($_GET['pay_period_id'])) {

            $pay_period_id = $_GET['pay_period_id'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM pay_period WHERE pay_period_id='$pay_period_id'"));

            foreach($inputs as $input) {
                $$input = $get_contact[$input];
            }

        ?>
        <input type="hidden" id="pay_period_id" name="pay_period_id" value="<?php echo $pay_period_id ?>" />
        <?php   }      ?>



<div class="panel-group" id="accordion2">
<?php
$k=0;
if(isset($value['config_field'])) {
    $get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
    $value_config = ','.$get_field_config[$value['config_field']].',';
    foreach($value['data'] as $tab_name => $tabs) {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field<?php echo $k; ?>" >
                    <?php echo $tab_name; ?><span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>
        <div id="collapse_field<?php echo $k; ?>" class="panel-collapse collapse">
            <div class="panel-body">
                    <?php
                        foreach($tabs as $field) {
                            if (strpos($value_config, ','.$field[2].',') !== FALSE) {
                                echo get_field($field, @$$field[2], $dbc);
                            }
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <?php
       $k++;
    }

}


?>
</div>

        <div class="form-group">
          <div class="col-sm-4">
              <p><span class="hp-red pull-right"><em>Required Fields *</em></span></p>
          </div>
          <div class="col-sm-8"></div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 clearfix">
                <a href="pay_period.php" class="btn brand-btn pull-right">Back</a>
            </div>
          <div class="col-sm-8">
            <button type="submit" name="submit" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
          </div>
        </div>

        

    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>
