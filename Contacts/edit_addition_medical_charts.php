<?php if($field_option == 'Client Medical Charts') {
	include_once 'config_mc.php';
	include_once 'config_mc_functions.php';

	$k=0;
	if(!empty($_GET['edit'])) {
		$client = $_GET['edit'];
	}
	$search_query = '';
	$medical_charts_date = '';
	if(!empty($_POST['load_medchartdate'])) {
		$medical_charts_date = $_POST['medical_charts_date'];
		$search_query = " AND `date` = '".$medical_charts_date."'";
	}
	if(!empty($_POST['export_medchart'])) {
		$client = $_POST['client'];
		$bowel_movement_id = $_POST['bowel_movement_id'];
		$seizure_record_id = $_POST['seizure_record_id'];
		$daily_water_temp_id = $_POST['daily_water_temp_id'];
		$blood_glucose_id = $_POST['blood_glucose_id'];
		if(!empty($_POST['medical_charts_date'])) {
			$medical_charts_date = $_POST['medical_charts_date'];
			$search_query = " AND `date` = '".$medical_charts_date."'";
		}
		include('../ClientInfo/add_medical_charts_pdf.php');
	}
	?>

	<input type="hidden" id="client" name="client" value="<?php echo $client; ?>" />
	<input type="hidden" id="edit" name="edit" value="<?php echo $client; ?>" />
	<input type="hidden" id="submit_type" name="submit_type" value="medical_charts" />

	<center>
		<div class="form-group">
			<div class="col-sm-4 col-sm-offset-4"><label class="control-label">Date:</label>
				<input type="text" class="form-control inline datepicker" name="medical_charts_date" value="<?= $medical_charts_date ?>">&nbsp;&nbsp;
				<button type="submit" name="load_medchartdate" value="load_medchartdate" onclick="$('[name=subtab]').val('Charts');" class="btn brand-btn mobile-block">Submit</button></div>
			<div class="col-sm-4">
				<button type="submit" name="add_medchart" value="add_medchart" onclick="$('[name=subtab]').val('Charts');" class="btn brand-btn mobile-block pull-right">Add Medical Chart</button>
				<button type="submit" name="export_medchart" value="export_medchart" onclick="$('[name=subtab]').val('Charts');" class="btn brand-btn mobile-block pull-right">Export to PDF</button>
			</div>
		</div>
	</center>
	<div class="clearfix"></div>

	<?php
	//Bowel Movement
	$value = $config_mc['settings']['Choose Fields for Bowel Movement'];

	$inputs = get_all_inputs($value['data']);

	foreach($inputs as $input) {
		$$input = '';
	}

	if(empty($_POST['add_medchart'])) {
		$get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `bowel_movement` WHERE `client`='".$_GET['edit']."' AND `deleted` = 0".$search_query." ORDER BY `date` DESC"));

		foreach($inputs as $input) {
			$$input = $get_contact[$input];
		}
		$bowel_movement_id = $get_contact['bowel_movement_id'];
	}
	$client = $_GET['edit'];

	?>
	<input type="hidden" id="bowel_movement_id" name="bowel_movement_id" value="<?php echo $bowel_movement_id; ?>" />
	<input type="hidden" id="bowel_movement_client" name="bowel_movement_client" value="<?php echo $client; ?>" />

	<?php
	if(isset($value['config_field'])) {
		$get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
		$value_config = ','.$get_field_config[$value['config_field']].',';
		foreach($value['data'] as $tab_name => $tabs) {
		?>
		<h5><?php echo $tab_name; ?></h5>
		<?php
			foreach($tabs as $field) {
				if (strpos($value_config, ','.$field[2].',') !== FALSE) {
					echo get_field($field, @$$field[2], $dbc);
				}
			}
		?>
		<?php
		   $k++;
		}
	}
	//Bowel Movement

	//Seizure Record
	$value = $config['settings']['Choose Fields for Seizure Record'];

	$inputs = get_all_inputs($value['data']);

	foreach($inputs as $input) {
		$$input = '';
	}

	if(empty($_POST['add_medchart'])) {
		$get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `seizure_record` WHERE `client`='".$_GET['edit']."' AND `deleted` = 0".$search_query." ORDER BY `date` DESC"));

		foreach($inputs as $input) {
			$$input = $get_contact[$input];
		}
		$seizure_record_id = $get_contact['seizure_record_id'];
	}
	$client = $_GET['edit'];

	?>

	<?php
	if(isset($value['config_field'])) {
		$get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
		$value_config = ','.$get_field_config[$value['config_field']].',';
		foreach($value['data'] as $tab_name => $tabs) {
		?>
		<h5><?php echo $tab_name; ?></h5>
		<?php
			foreach($tabs as $field) {
				if (strpos($value_config, ','.$field[2].',') !== FALSE) {
					echo get_field($field, @$$field[2], $dbc);
				}
			}
		?>
		<?php
		   $k++;
		}
	}
	//Seizure Record

	//Daily Water Temp
	$value = $config['settings']['Choose Fields for Daily Water Temp'];

	$inputs = get_all_inputs($value['data']);

	foreach($inputs as $input) {
		$$input = '';
	}

	if(empty($_POST['add_medchart'])) {
		$get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `daily_water_temp` WHERE `client`='".$_GET['edit']."' AND `deleted` = 0".$search_query." ORDER BY `date` DESC"));

		foreach($inputs as $input) {
			$$input = $get_contact[$input];
		}
		$daily_water_temp_id = $get_contact['daily_water_temp_id'];
	}
	$client = $_GET['edit'];

	?>

	<?php
	if(isset($value['config_field'])) {
		$get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
		$value_config = ','.$get_field_config[$value['config_field']].',';
		foreach($value['data'] as $tab_name => $tabs) {
		?>
		<h5><?php echo $tab_name; ?></h5>
		<input type="hidden" id="daily_water_temp_id" name="daily_water_temp_id" value="<?php echo $daily_water_temp_id; ?>" />
		<input type="hidden" id="daily_water_temp_client" name="daily_water_temp_client" value="<?php echo $client; ?>" />
			<?php
				foreach($tabs as $field) {
					if (strpos($value_config, ','.$field[2].',') !== FALSE) {
						echo get_field($field, @$$field[2], $dbc);
					}
				}
			?>
		<?php
		   $k++;
		}
	}
	//Daily Water Temp

	//Blood Glucose
	$value = $config['settings']['Choose Fields for Blood Glucose'];

	$inputs = get_all_inputs($value['data']);

	foreach($inputs as $input) {
		$$input = '';
	}

	if(empty($_POST['add_medchart'])) {
		$get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `blood_glucose` WHERE `client`='".$_GET['edit']."' AND `deleted` = 0".$search_query." ORDER BY `date` DESC"));

		foreach($inputs as $input) {
			$$input = $get_contact[$input];
		}
		$blood_glucose_id = $get_contact['blood_glucose_id'];
	}
	$client = $_GET['edit'];

	?>

	<?php
	if(isset($value['config_field'])) {
		$get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
		$value_config = ','.$get_field_config[$value['config_field']].',';
		foreach($value['data'] as $tab_name => $tabs) {
		?>
		<h5><?php echo $tab_name; ?></h5>
		<input type="hidden" id="blood_glucose_id" name="blood_glucose_id" value="<?php echo $blood_glucose_id; ?>" />
		<input type="hidden" id="blood_glucose_client" name="blood_glucose_client" value="<?php echo $client; ?>" />
			<?php
				foreach($tabs as $field) {
					if (strpos($value_config, ','.$field[2].',') !== FALSE) {
						echo get_field($field, @$$field[2], $dbc);
					}
				}
			?>
		<?php
		   $k++;
		}
	}
	//Blood Glucose
	$query_medical_charts = mysqli_query($dbc,"SELECT accordion, IFNULL(subtab,'Main') subtab, contacts FROM field_config_contacts WHERE tile_name = '".FOLDER_NAME."' AND tab='$url_category' AND contacts != 'Category,' AND `accordion` IS NOT NULL AND `order` IS NOT NULL AND `subtab` = 'Medical Charts' ORDER BY IFNULL(`subtab`,'Main')='$subtab', `order`");

	$j=0;
	if(IFRAME_PAGE) {
		$j = 100;
	}

	while($row_medical_charts = mysqli_fetch_array($query_medical_charts)) {
		$accordion = $row_medical_charts['accordion'];
		$value_config = ','.$row_medical_charts['contacts'].',';
		$edit_config = $value_config;
		$visible = '';
		if($accordion != '' && $accordion != 'Medical Charts') { ?>
			<div class="panel panel-default" <?php echo ($subtab == $row_medical_charts['subtab'] || count($subtab_list) == 1 ? $visible : 'style="display:none;"'); ?>>
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2<?php echo (IFRAME_PAGE ? '_IF' : ''); ?>" href="#collapse_<?php echo $j;?>" >
							<?php echo $row_medical_charts['accordion']; ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_<?php echo $j;?>" class="panel-collapse collapse">
					<div class="panel-body">
					<?php if (strpos($value_config, ','."Client Height".',') !== FALSE) { ?>
						<div class="form-group">
						<label for="client_height" class="col-sm-4 control-label">Client Height:</label>
						<div class="col-sm-8">
						  <input <?php echo (strpos($edit_config, ','."Client Height".',') === false ? 'readonly' : ''); ?> name="medchart_client_height" value="<?php echo $client_height; ?>" type="text" class="form-control">
						</div>
						</div>
					<?php } ?>

					<?php if (strpos($value_config, ','."Client Weight".',') !== FALSE) { ?>
						<div class="form-group">
						<label for="client_weight" class="col-sm-4 control-label">Client Weight:</label>
						<div class="col-sm-8">
						  <input <?php echo (strpos($edit_config, ','."Client Weight".',') === false ? 'readonly' : ''); ?> name="medchart_client_weight" value="<?php echo $client_weight; ?>" type="text" class="form-control">
						</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php }
		$j++;
	}
} ?>