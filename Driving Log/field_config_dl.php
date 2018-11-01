<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('driving_log');
error_reporting(0);

if (isset($_POST['submit'])) {

	//Logo
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    $pos_logo = htmlspecialchars($_FILES["pos_logo"]["name"], ENT_QUOTES);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='dlog_logo'"));
    if($get_config['configid'] > 0) {
		if($pos_logo == '') {
			$logo_update = $_POST['logo_file'];
		} else {
			$logo_update = $pos_logo;
		}
		move_uploaded_file($_FILES["pos_logo"]["tmp_name"],"download/" . $logo_update);
        $query_update_employee = "UPDATE `general_configuration` SET value = '$logo_update' WHERE name='dlog_logo'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        move_uploaded_file($_FILES["pos_logo"]["tmp_name"], "download/" . $_FILES["pos_logo"]["name"]) ;
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('dlog_logo', '$pos_logo')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Logo

	// Main Address
    $main_office_address_dl = filter_var($_POST['main_office_address_dl'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='main_office_address_dl'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$main_office_address_dl' WHERE name='main_office_address_dl'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('main_office_address_dl', '$main_office_address_dl')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    // Main Address

    // Cycle Times
    $driving_log_cycle_times = filter_var(implode(',', $_POST['cycle_times']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='driving_log_cycle_times'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$driving_log_cycle_times' WHERE name='driving_log_cycle_times'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('driving_log_cycle_times', '$driving_log_cycle_times')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    // Cycle Times

	// Mileage Fields
    $mileage_fields = filter_var(implode(',', $_POST['mileage_fields']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='mileage_fields'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$mileage_fields' WHERE name='mileage_fields'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('mileage_fields', '$mileage_fields')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
	// Mileage Fields

    // Mileage PDF Settings
    $pdf_logo = $_POST['logo_file'];
    if(!empty($_FILES['pdf_logo']['name'])) {
        $pdf_logo = htmlspecialchars($_FILES['pdf_logo']['name'], ENT_QUOTES);
        if (!file_exists('download')) {
            mkdir('download', 0777, true);
        }
        move_uploaded_file($_FILES['pdf_logo']['tmp_name'], '../Driving Log/download/'.$pdf_logo);
    }

    $header_text = filter_var(htmlentities($_POST['header_text']),FILTER_SANITIZE_STRING);
    $header_align = filter_var($_POST['header_align'],FILTER_SANITIZE_STRING);
    $header_font = filter_var($_POST['header_font'],FILTER_SANITIZE_STRING);
    $header_size = filter_var($_POST['header_size'],FILTER_SANITIZE_STRING);
    $header_color = filter_var($_POST['header_color'],FILTER_SANITIZE_STRING);

    $footer_text = filter_var(htmlentities($_POST['footer_text']),FILTER_SANITIZE_STRING);
    $footer_align = filter_var($_POST['footer_align'],FILTER_SANITIZE_STRING);
    $footer_font = filter_var($_POST['footer_font'],FILTER_SANITIZE_STRING);
    $footer_size = filter_var($_POST['footer_size'],FILTER_SANITIZE_STRING);
    $footer_color = filter_var($_POST['footer_color'],FILTER_SANITIZE_STRING);

    $body_font = filter_var($_POST['body_font'],FILTER_SANITIZE_STRING);
    $body_size = filter_var($_POST['body_size'],FILTER_SANITIZE_STRING);
    $body_color = filter_var($_POST['body_color'],FILTER_SANITIZE_STRING);

    $pdf_settings = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `driving_log_mileage_pdf_setting`"));
    if(!empty($pdf_settings)) {
        $query = "UPDATE `driving_log_mileage_pdf_setting` SET `pdf_logo` = '$pdf_logo', `header_text` = '$header_text', `header_align` = '$header_align', `header_font` = '$header_font', `header_size` = '$header_size', `header_color` = '$header_color', `footer_text` = '$footer_text', `footer_align` = '$footer_align', `footer_font` = '$footer_font', `footer_size` = '$footer_size', `footer_color` = '$footer_color', `body_font` = '$body_font', `body_size` = '$body_size', `body_color` = '$body_color'";
    } else {
        $query = "INSERT INTO `driving_log_mileage_pdf_setting` (`pdf_logo`, `header_text`, `header_align`, `header_font`, `header_size` ,`header_color`, `footer_text`, `footer_align`, `footer_font`, `footer_size`, `footer_color`, `body_font`, `body_size`, `body_color`) VALUES ('$pdf_logo', '$header_text', '$header_align', '$header_font', '$header_size' ,'header_color', '$footer_text', '$footer_align', '$footer_font', '$footer_size', '$footer_color', '$body_font', '$body_size', '$body_color')";
    }
    mysqli_query($dbc, $query);
    // Mileage PDF Settings


    echo '<script type="text/javascript"> window.location.replace("field_config_dl.php"); </script>';

}
?>
<script type="text/javascript">
$(document).ready(function() {
   $('.clickopen').click();
});
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1>Driving Log</h1>
<div class="gap-top double-gap-bottom"><a href="driving_log_tiles.php" class="btn config-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<div class="panel-group" id="accordion2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_logo" class='clickopen'>
                    Main Office Address (Preset)<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_logo" class="panel-collapse collapse">
            <div class="panel-body">
             <div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Main Office Address (Preset):</label>
                    <div class="col-sm-8">
                      <input name="main_office_address_dl" value="<?php if(get_config($dbc, 'main_office_address_dl') == '' || get_config($dbc, 'main_office_address_dl') == NULL ) { echo ""; } else { echo get_config($dbc, 'main_office_address_dl'); } ?>" type="text" class="form-control"/>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_cycle">
                    Cycle Times<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_cycle" class="panel-collapse collapse">
            <div class="panel-body">
            <?php $cycle_times = empty(get_config($dbc, 'driving_log_cycle_times')) ? explode(',', 'Cycle 1,Cycle 2') : explode(',', get_config($dbc, 'driving_log_cycle_times')); ?>
                <div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Cycle 1(7 days : 70 hours):</label>
                    <div class="col-sm-8">
                      <label class="form-checkbox"><input name="cycle_times[]" value="Cycle 1" type="checkbox"<?= in_array('Cycle 1', $cycle_times) ? 'checked="checked"' : '' ?>/></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Cycle 2(14 days : 120 hours):</label>
                    <div class="col-sm-8">
                      <label class="form-checkbox"><input name="cycle_times[]" value="Cycle 2" type="checkbox"<?= in_array('Cycle 2', $cycle_times) ? 'checked="checked"' : '' ?>/></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_logo2" >
                    PDF Logo<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_logo2" class="panel-collapse collapse">
            <div class="panel-body">
                <?php
                $pos_logo = get_config($dbc, 'dlog_logo');
                ?>

                <div class="form-group">
                <label for="file[]" class="col-sm-4 control-label">Upload Logo
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
                </span>
                :</label>
                <div class="col-sm-8">
                <?php if($pos_logo != '') {
                    echo '<a href="download/'.$pos_logo.'" target="_blank">View</a>';
                    ?>
                    <input type="hidden" name="logo_file" value="<?php echo $pos_logo; ?>" />
                    <input name="pos_logo" type="file" data-filename-placement="inside" class="form-control" />
                  <?php } else { ?>
                  <input name="pos_logo" type="file" data-filename-placement="inside" class="form-control" />
                  <?php } ?>
                </div>
                </div>

            </div>
        </div>
    </div>

	<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_mileage" >
                    Mileage Tracking<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_mileage" class="panel-collapse collapse">
            <div class="panel-body">
                <?php $mileage_fields = explode(',',get_config($dbc, 'mileage_fields')); ?>
                <div class="form-group">
					<label class="col-sm-4 control-label">Fields for Mileage:</label>
					<div class="col-sm-8">
						<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="staff" <?= in_array('staff',$mileage_fields) ? 'checked' : '' ?>> Driver</label>
						<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="startdate" <?= in_array('startdate',$mileage_fields) ? 'checked' : '' ?>> Start Date</label>
						<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="enddate" <?= in_array('enddate',$mileage_fields) ? 'checked' : '' ?>> End Date</label>
						<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="category" <?= in_array('category',$mileage_fields) ? 'checked' : '' ?>> Category</label>
						<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="details" <?= in_array('details',$mileage_fields) ? 'checked' : '' ?>> Details</label>
						<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="rate" <?= in_array('rate',$mileage_fields) ? 'checked' : '' ?>> Rate</label>
						<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="contact" <?= in_array('contact',$mileage_fields) ? 'checked' : '' ?>> Client</label>
						<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="double_mileage" <?= in_array('double_mileage',$mileage_fields) ? 'checked' : '' ?>> Mileage X 2</label>
					</div>
                </div>
                <div class="form-group">
					<label class="col-sm-4 control-label">What can Mileage be Attached to?</label>
					<div class="col-sm-8">
						<?php if(tile_enabled($dbc, 'ticket')['user_enabled'] == 1) { ?>
							<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="tickets" <?= in_array('tickets',$mileage_fields) ? 'checked' : '' ?>> <?= TICKET_TILE ?></label>
						<?php } ?>
						<?php if(tile_enabled($dbc, 'project')['user_enabled'] == 1) { ?>
							<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="projects" <?= in_array('projects',$mileage_fields) ? 'checked' : '' ?>> <?= PROJECT_TILE ?></label>
						<?php } ?>
						<?php if(tile_enabled($dbc, 'tasks')['user_enabled'] == 1) { ?>
							<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="tasks" <?= in_array('tasks',$mileage_fields) ? 'checked' : '' ?>> <?= TASK_TILE ?></label>
						<?php } ?>
						<?php if(tile_enabled($dbc, 'equipment')['user_enabled'] == 1) { ?>
							<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="equipment" <?= in_array('equipment',$mileage_fields) ? 'checked' : '' ?>> Equipment</label>
						<?php } ?>
						<?php if(tile_enabled($dbc, 'checklist')['user_enabled'] == 1) { ?>
							<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="checklist" <?= in_array('checklist',$mileage_fields) ? 'checked' : '' ?>> Checklists</label>
						<?php } ?>
						<?php if(tile_enabled($dbc, 'expense')['user_enabled'] == 1) { ?>
							<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="expense" <?= in_array('expense',$mileage_fields) ? 'checked' : '' ?>> Expenses</label>
						<?php } ?>
						<?php if(tile_enabled($dbc, 'agenda_meeting')['user_enabled'] == 1) { ?>
							<label class="form-checkbox"><input type="checkbox" name="mileage_fields[]" value="meetings" <?= in_array('meetings',$mileage_fields) ? 'checked' : '' ?>> Meetings</label>
						<?php } ?>
					</div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_mileage_pdf" >
                    Mileage PDF Settings<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_mileage_pdf" class="panel-collapse collapse">
            <div class="panel-body">
                <?php
                $pdf_settings = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `driving_log_mileage_pdf_setting`"));

                $pdf_logo = !empty($pdf_settings['pdf_logo']) ? $pdf_settings['pdf_logo'] : '';

                $header_text = !empty($pdf_settings['header_text']) ? $pdf_settings['header_text'] : '';
                $header_align = !empty($pdf_settings['header_align']) ? $pdf_settings['header_align'] : 'R';
                $header_font = !empty($pdf_settings['header_font']) ? $pdf_settings['header_font'] : 'helvetica';
                $header_size = !empty($pdf_settings['header_size']) ? $pdf_settings['header_size'] : 9;
                $header_color = !empty($pdf_settings['header_color']) ? $pdf_settings['header_color'] : '#000000';

                $footer_text = !empty($pdf_settings['footer_text']) ? $pdf_settings['footer_text'] : '';
                $footer_align = !empty($pdf_settings['footer_align']) ? $pdf_settings['footer_align'] : 'C';
                $footer_font = !empty($pdf_settings['footer_font']) ? $pdf_settings['footer_font'] : 'helvetica';
                $footer_size = !empty($pdf_settings['footer_size']) ? $pdf_settings['footer_size'] : 9;
                $footer_color = !empty($pdf_settings['footer_color']) ? $pdf_settings['footer_color'] : '#000000';

                $body_font = !empty($pdf_settings['body_font']) ? $pdf_settings['body_font'] : 'helvetica';
                $body_size = !empty($pdf_settings['body_size']) ? $pdf_settings['body_size'] : 9;
                $body_color = !empty($pdf_settings['body_color']) ? $pdf_settings['body_color'] : '#000000';
                ?>
                <h4>Logo for PDF</h4>
                <div class="form-group">
                    <label for="pdf_logo" class="col-sm-4 control-label">
                        Upload Logo:
                    </label>
                    <div class="col-sm-8">
                        <?php if(!empty($pdf_logo)) { ?>
                            <a href="<?= WEBSITE_URL ?>/Driving Log/download/<?= $pdf_logo ?>" target="_blank">View</a>
                            <input type="hidden" name="logo_file" value="<?= $pdf_logo ?>">
                        <?php } ?>
                        <input type="file" name="pdf_logo" class="form-control">
                    </div>
                </div>

                <h4>Header Settings</h4>
                <div class="form-group">
                    <label for="header_text" class="col-sm-4 control-label">
                        Header Text:
                    </label>
                    <div class="col-sm-8">
                        <textarea name="header_text" class="form-control"><?= html_entity_decode($header_text) ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="header_align" class="col-sm-4 control-label">
                        Header Align
                    </label>
                    <div class="col-sm-8">
                        <select name="header_align" class="chosen-select-deselect form-control">
                            <option></option>
                            <option <?= $header_align == 'L' ? 'selected' : '' ?> value="L">Left</option>
                            <option <?= $header_align == 'C' ? 'selected' : '' ?> value="C">Center</option>
                            <option <?= $header_align == 'R' ? 'selected' : '' ?> value="R">Right</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="header_font" class="col-sm-4 control-label">
                        Header Font:
                    </label>
                    <div class="col-sm-8">
                        <select name="header_font" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php $font_array = array('courier'=>'Courier','helvetica'=>'Helvetica','times'=>'Times New Roman','zapfdingbats'=>'Zapf Dingbats','OpenSans'=>'Open Sans','Roboto'=>'Roboto','Encode Sans, sans-serif' => 'Encode Sans, sans-serif', 'Slabo, serif' => 'Slabo', 'Montserrat, sans-serif' => 'Montserrat', 'Raleway, sans-serif' => 'Raleway', 'Merriweather, sans-serif' => 'Merriweather', 'Lora, sans-serif' => 'Lora', 'Nunito, sans-serif' => 'Nunito', 'Karla, sans-serif' => 'Karla');
                                ksort($font_array);
                                foreach($font_array as $font_value => $font) { ?>
                                    <option <?= $header_font == $font_value ? 'selected' : '' ?> value="<?= $font_value ?>"><?= $font ?></option>
                                <?php }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="header_size" class="col-sm-4 control-label">
                        Header Size:
                    </label>
                    <div class="col-sm-8">
                        <select name="header_size" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php for($i = 9; $i < 50; $i++) { ?>
                                <option <?= $header_size == $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>pt</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="header_color" class="col-sm-4 control-label">
                        Header Color:
                    </label>
                    <div class="col-sm-1">
                        <input type="color" name="header_color_picker" value="<?= $header_color ?>" class="form-control" onchange="colorCodeChange(this);">
                    </div>
                    <div class="col-sm-7">
                        <input type="text" name="header_color" value="<?= $header_color ?>" class="form-control">
                    </div>
                </div>

                <h4>Footer Settings</h4>
                <div class="form-group">
                    <label for="footer_text" class="col-sm-4 control-label">
                        Footer Text:
                    </label>
                    <div class="col-sm-8">
                        <textarea name="footer_text" class="form-control"><?= html_entity_decode($footer_text) ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="footer_align" class="col-sm-4 control-label">
                        Footer Align
                    </label>
                    <div class="col-sm-8">
                        <select name="footer_align" class="chosen-select-deselect form-control">
                            <option></option>
                            <option <?= $footer_align == 'L' ? 'selected' : '' ?> value="L">Left</option>
                            <option <?= $footer_align == 'C' ? 'selected' : '' ?> value="C">Center</option>
                            <option <?= $footer_align == 'R' ? 'selected' : '' ?> value="R">Right</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="footer_font" class="col-sm-4 control-label">
                        Footer Font:
                    </label>
                    <div class="col-sm-8">
                        <select name="footer_font" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php $font_array = array('courier'=>'Courier','helvetica'=>'Helvetica','times'=>'Times New Roman','zapfdingbats'=>'Zapf Dingbats','OpenSans'=>'Open Sans','Roboto'=>'Roboto','Encode Sans, sans-serif' => 'Encode Sans, sans-serif', 'Slabo, serif' => 'Slabo', 'Montserrat, sans-serif' => 'Montserrat', 'Raleway, sans-serif' => 'Raleway', 'Merriweather, sans-serif' => 'Merriweather', 'Lora, sans-serif' => 'Lora', 'Nunito, sans-serif' => 'Nunito', 'Karla, sans-serif' => 'Karla');
                                ksort($font_array);
                                foreach($font_array as $font_value => $font) { ?>
                                    <option <?= $footer_font == $font_value ? 'selected' : '' ?> value="<?= $font_value ?>"><?= $font ?></option>
                                <?php }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="footer_size" class="col-sm-4 control-label">
                        Footer Size:
                    </label>
                    <div class="col-sm-8">
                        <select name="footer_size" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php for($i = 9; $i < 50; $i++) { ?>
                                <option <?= $footer_size == $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>pt</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="footer_color" class="col-sm-4 control-label">
                        Footer Color:
                    </label>
                    <div class="col-sm-1">
                        <input type="color" name="footer_color_picker" value="<?= $footer_color ?>" class="form-control" onchange="colorCodeChange(this);">
                    </div>
                    <div class="col-sm-7">
                        <input type="text" name="footer_color" value="<?= $footer_color ?>" class="form-control">
                    </div>
                </div>

                <h4>Body Settings</h4>
                <div class="form-group">
                    <label for="body_font" class="col-sm-4 control-label">
                        Body Font:
                    </label>
                    <div class="col-sm-8">
                        <select name="body_font" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php $font_array = array('courier'=>'Courier','helvetica'=>'Helvetica','times'=>'Times New Roman','zapfdingbats'=>'Zapf Dingbats','OpenSans'=>'Open Sans','Roboto'=>'Roboto','Encode Sans, sans-serif' => 'Encode Sans, sans-serif', 'Slabo, serif' => 'Slabo', 'Montserrat, sans-serif' => 'Montserrat', 'Raleway, sans-serif' => 'Raleway', 'Merriweather, sans-serif' => 'Merriweather', 'Lora, sans-serif' => 'Lora', 'Nunito, sans-serif' => 'Nunito', 'Karla, sans-serif' => 'Karla');
                                ksort($font_array);
                                foreach($font_array as $font_value => $font) { ?>
                                    <option <?= $body_font == $font_value ? 'selected' : '' ?> value="<?= $font_value ?>"><?= $font ?></option>
                                <?php }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="body_size" class="col-sm-4 control-label">
                        Body Size:
                    </label>
                    <div class="col-sm-8">
                        <select name="body_size" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php for($i = 9; $i < 50; $i++) { ?>
                                <option <?= $body_size == $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>pt</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="body_color" class="col-sm-4 control-label">
                        Body Color:
                    </label>
                    <div class="col-sm-1">
                        <input type="color" name="body_color_picker" value="<?= $body_color ?>" class="form-control" onchange="colorCodeChange(this);">
                    </div>
                    <div class="col-sm-7">
                        <input type="text" name="body_color" value="<?= $body_color ?>" class="form-control">
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<div class="form-group">
    <div class="col-sm-6">
        <a href="driving_log_tiles.php" class="btn config-btn btn-lg">Back</a>
		<!--<a href="driving_log_tiles.php" class="btn config-btn btn-lg pull-right">Back</a>-->
	</div>
	<div class="col-sm-6">
        <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
    </div>
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>