<?php
include ('../include.php');
include 'config.php';

$value = $config['settings']['Choose Fields for Holidays'];

if(isset($_GET['action']) && $_GET['action'] == 'delete') {
	    $date_of_archival = date('Y-m-d');
    mysqli_query($dbc, "UPDATE holidays SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE holidays_id=".$_GET['holidays_id']);

    echo '<script type="text/javascript"> window.location.replace("holidays.php"); </script>';
}

if (isset($_POST['submit'])) {

    $inputs = get_post_inputs($value['data']);
    $files = get_post_uploads($value['data']);
    move_files($files);

    if(empty($_POST['holidays_id'])) {
        $query_insert_vendor = prepare_insert($inputs, 'holidays');
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $bowel_movement_id = mysqli_insert_id($dbc);
        $url = 'Added';
    } else {
        $holidays_id = $_POST['holidays_id'];
        $query_update_vendor = prepare_update($inputs, 'holidays', 'holidays_id', $holidays_id);
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $url = 'Updated';
    }

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }

    echo '<script type="text/javascript"> window.location.replace("holidays.php"); </script>';

}
?>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('timesheet');
?>
<div class="container">
  <div class="row">

    <h1 class="triple-pad-bottom">Holiday</h1>

    <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php

        $inputs = get_all_inputs($value['data']);

        foreach($inputs as $input) {
            $$input = '';
        }

        if(!empty($_GET['holidays_id'])) {

            $holidays_id = $_GET['holidays_id'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM holidays WHERE holidays_id='$holidays_id'"));
            $name = $get_contact['name'];
            $date = $get_contact['date'];
            $paid = $get_contact['paid'];

            foreach($inputs as $input) {
                $$input = $get_contact[$input];
            }

        ?>
        <input type="hidden" id="holidays_id" name="holidays_id" value="<?php echo $holidays_id ?>" />
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
								if($field[2] == 'name') {
									$defined_holidays = [];
									include('../Calendar/defined_holidays.php'); ?>
									<script>
                                    $(document).on('change', 'select[name="defined_holidays"]', function() { use_defined_holiday(this); });
									function use_defined_holiday(select) {
										if(select.value == 'CUSTOM') {
											$(select).closest('div').find('.select2').hide();
											$(select).closest('div').find('input').show().focus();
										} else {
											var row = $(select).closest('.form-group');
											var choice = $(select).find('option:selected');
											$.ajax({
												url: '../ajax_dates.php?action=next_occurrence',
												method: 'POST',
												data: { day: choice.data('day'), month: choice.data('month'), week: choice.data('week'), weekday: choice.data('weekday'), name: choice.val() },
												success: function(response) {
													$('[name="date"]').val(response);
												}
											});
											$('[name="name"]').val(choice.val());
											$('[name="paid"]').prop('checked',choice.data('paid'));
										}
									}
									</script>
									<div class="form-group">
										<label class="col-sm-4 control-label">Holiday Name:</label>
										<div class="col-sm-8">
											<?php if($name == '' && $date == '') { ?>
												<select class="chosen-select-deselect" name="defined_holidays"><option></option>
													<option value="CUSTOM">Custom Holiday</option>
													<?php foreach($defined_holidays as $defined) { ?>
														<option data-day="<?= $defined['day'] ?>" data-week="<?= $defined['week'] ?>" data-weekday="<?=$defined['weekday'] ?>" data-month="<?= $defined['month'] ?>" data-paid="<?= $defined['paid'] ?>" value="<?= $defined['name'] ?>"><?= $defined['label'] ?></option>
													<?php } ?>
												</select>
												<input type="text" name="name" value="<?= $get_contact['name'] ?>" class="form-control" style="display:none;">
											<?php } else { ?>
												<input type="text" name="name" value="<?= $get_contact['name'] ?>" class="form-control">
											<?php } ?>
										</div>
									</div>
								<?php } else {
									echo get_field($field, @$$field[2], $dbc);
								}
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
                <a href="holidays.php" class="btn brand-btn pull-right">Back</a>
            </div>
          <div class="col-sm-8">
            <button type="submit" name="submit" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
          </div>
        </div>



    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>
