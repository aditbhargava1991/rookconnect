<?php
/*
 * Add Pay Period
 * Included From
        - tab_pay_period.php
 */
include ('../include.php');
include 'config.php';

$value = $config['settings']['Choose Fields for Pay Period'];

if(isset($_GET['action']) && $_GET['action'] == 'delete') {
    mysqli_query($dbc, "DELETE FROM pay_period WHERE pay_period_id=".$_GET['pay_period_id']);
    echo '<script type="text/javascript"> window.location.replace("main.php?navtab=pay_period"); </script>';
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

    echo '<script type="text/javascript"> window.location.replace("main.php?navtab=pay_period"); </script>';
}
?>
<style>.control-label { text-align:left !important; }</style>
</head>

<body>
<?php checkAuthorised('timesheet'); ?>

<div class="container">
    <div class="row">
        <h3 class="inline">Pay Period</h3>
        <div class="pull-right double-gap-top"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="no-toggle" data-placement="bottom" width="25" /></a></div>
        <div class="clearfix"></div>
        <hr />

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form"><?php
            $inputs = get_all_inputs($value['data']);

            foreach($inputs as $input) {
                $$input = '';
            }

            if(!empty($_GET['pay_period_id'])) {
                $pay_period_id = $_GET['pay_period_id'];
                $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM pay_period WHERE pay_period_id='$pay_period_id'"));

                foreach($inputs as $input) {
                    $$input = $get_contact[$input];
                } ?>
                
                <input type="hidden" id="pay_period_id" name="pay_period_id" value="<?php echo $pay_period_id ?>" /><?php
            } ?>
            
            <div class=""><?php
                if(isset($value['config_field'])) {
                    $get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
                    $value_config = ','.$get_field_config[$value['config_field']].',';
                    foreach($value['data'] as $tab_name => $tabs) {
                        foreach($tabs as $field) {
                            if (strpos($value_config, ','.$field[2].',') !== FALSE) {
                                echo get_field($field, @$$field[2], $dbc);
                            }
                        }
                    }
                } ?>
            </div>
            
            <div class="form-group">
                <div class="col-xs-6">
                    <?php if(!empty($_GET['pay_period_id'])) { ?><a href="add_pay_period.php?action=delete&pay_period_id=<?= $pay_period_id ?>" onclick="return confirm('Are you sure you want to delete this Pay Period?')"><img class="no-margin small" src="../img/icons/trash-icon-red.png" alt="Archive Task" width="30"></a><?php } ?>
                </div>
                <div class="col-xs-6 text-right">
                    <a href="main.php?navtab=pay_period" class="btn brand-btn">Cancel</a>
                    <button type="submit" name="submit" value="submit" class="btn brand-btn">Submit</button>
                </div>
                <div class="clearfix"></div>
            </div>
        </form>
        
    </div><!-- .row -->
</div><!-- .container -->