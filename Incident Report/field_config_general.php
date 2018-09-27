<?php if (isset($_POST['submit'])) {
	//Save to Journal
	$inc_rep_save_journal = filter_var($_POST['inc_rep_save_journal'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'inc_rep_save_journal', $inc_rep_save_journal);

	//Saved Tab
	$inc_rep_saved_tab = filter_var($_POST['inc_rep_saved_tab'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'inc_rep_saved_tab', $inc_rep_saved_tab);

	//Rows per Page
	$inc_rep_rows_per_page = filter_var($_POST['inc_rep_rows_per_page'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'inc_rep_rows_per_page', $inc_rep_rows_per_page);

	//Logo
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	$logo = $_FILES["logo"]["name"];
	$pdf_header = filter_var(htmlentities($_POST['pdf_header']),FILTER_SANITIZE_STRING);
	$pdf_footer = filter_var(htmlentities($_POST['pdf_footer']),FILTER_SANITIZE_STRING);
	$pdf_notes = filter_var(htmlentities($_POST['pdf_notes']),FILTER_SANITIZE_STRING);
	$pdf_title = filter_var(htmlentities($_POST['pdf_title']),FILTER_SANITIZE_STRING);
	$incident_types = filter_var($_POST['incident_types'],FILTER_SANITIZE_STRING);
	$incident_report_dashboard = implode(',',$_POST['incident_report_dashboard']);
	if(isset($_POST['safety_report'])) {
		$safety_report = 1;
	} else {
		$safety_report = 0;
	}

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(*) AS rows FROM field_config_incident_report"));
	if($get_field_config['rows'] > 0) {
		if($logo == '') {
			$logo_update = $_POST['logo_file'];
		} else {
			$logo_update = $logo;
		}
		move_uploaded_file($_FILES["logo"]["tmp_name"],"download/" . $logo_update);
		$query_config = "UPDATE `field_config_incident_report` SET pdf_logo = '$logo_update', pdf_header = '$pdf_header', pdf_footer = '$pdf_footer', `pdf_title` = '$pdf_title', `pdf_notes` = '$pdf_notes', incident_report_dashboard = '$incident_report_dashboard', `incident_types`='$incident_types', `safety_report` = '$safety_report'";
		$result_update_employee = mysqli_query($dbc, $query_config);
	} else {
		move_uploaded_file($_FILES["logo"]["tmp_name"], "download/" . $_FILES["logo"]["name"]) ;
		$query_config = "INSERT INTO `field_config_incident_report` (`pdf_logo`, `pdf_header`, `pdf_footer`, `pdf_title`, `pdf_notes`, `incident_report_dashboard`, `incident_types`, `safety_report`) VALUES ('$logo', '$pdf_header', '$pdf_footer', '$pdf_title', '$pdf_notes', '$incident_report_dashboard', '$incident_types', '$safety_report')";
		$result_insert_config = mysqli_query($dbc, $query_config);
	}
	//Logo

	//Tile Settings
	$tile_name = $_POST['tile_name'];
	$tile_noun = $_POST['tile_noun'];
	$inc_rep_tile_name = $tile_name.'#*#'.$tile_noun;
	$num_rows = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `general_configuration` WHERE `name` = 'inc_rep_tile_name'"))['num_rows'];
	if($num_rows > 0) {
		mysqli_query($dbc, "UPDATE `general_configuration` SET `inc_rep_tile_name` = '$inc_rep_tile_name'");
	} else {
		mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('inc_rep_tile_name', '$inc_rep_tile_name')");
	}
	set_config($dbc, 'incident_report_tabs', implode(',',$_POST['incident_report_tabs']));
	set_config($dbc, 'incident_report_summary', implode(',',$_POST['incident_report_summary']));
	//Tile Settings

    echo '<script type="text/javascript"> window.location.replace(""); </script>';
} ?>
<input type="hidden" name="full_type" value="<?= $main_type ?>">
<input type="hidden" name="full_type_list" value="<?= $get_field_config['incident_types'] ?>">

<div class="form-group">
	<h4>Choose Fields for All <?= INC_REP_TILE ?></h4>
	<?php $value_config = ','.$get_field_config['incident_report_dashboard'].',';
	$type_config = $get_field_config['incident_types'];
	$safety_report = $get_field_config['safety_report']; ?>
	<div id='no-more-tables'>
	<h5><?= INC_REP_NOUN ?> Dashboards</h5>
	<table border='2' cellpadding='10' class='table'>
		<tr>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."Program".',') !== FALSE) { echo " checked"; } ?> value="Program" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Program
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."Project Type".',') !== FALSE) { echo " checked"; } ?> value="Project Type" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;<?= PROJECT_NOUN ?> Type
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;<?= PROJECT_NOUN ?>
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."Ticket".',') !== FALSE) { echo " checked"; } ?> value="Ticket" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;<?= TICKET_NOUN ?>
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."Member".',') !== FALSE) { echo " checked"; } ?> value="Member" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Member
			</td>
		</tr>
		<tr>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."Client".',') !== FALSE) { echo " checked"; } ?> value="Client" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Client
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Type Of Incident
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Staff
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up".',') !== FALSE) { echo " checked"; } ?> value="Follow Up" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Follow Up
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."Date of Happening".',') !== FALSE) { echo " checked"; } ?> value="Date of Happening" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Date of Happening
			</td>
		</tr>
		<tr>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."Date of Incident".',') !== FALSE) { echo " checked"; } ?> value="Date of Incident" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Date of Incident
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."Date Created".',') !== FALSE) { echo " checked"; } ?> value="Date Created" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Date Created
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."Location".',') !== FALSE) { echo " checked"; } ?> value="Location" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Location
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config, ','."PDF".',') !== FALSE) { echo " checked"; } ?> value="PDF" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;PDF
			</td>
		</tr>
	</table>
   </div>
  <div class="form-group">
	<label class="col-sm-4 control-label">Types of Incidents:<br /><em>Enter the types of incidents you can report, separated by commas.<br />Enclose the text with double quotes if there is a comma in the name.</em></label>
	<div class="col-sm-8">
		<input type="text" name="incident_types" value="<?= $type_config ?>" class="form-control">
	</div>
  </div>
  <div class="form-group">
  	<?php $inc_rep_saved_tab = get_config($dbc, 'inc_rep_saved_tab'); ?>
  	<label class="col-sm-4 control-label">Display Saved Tab:<br /><em>This will be a tab displaying all Saved <?= INC_REP_TILE ?>.</em></label>
  	<div class="col-sm-8">
  		<label class="form-checkbox"><input type="checkbox" name="inc_rep_saved_tab" value="1" <?= $inc_rep_saved_tab == 1 ? 'checked' : '' ?>> Enable</label>
  	</div>
  </div>
  <div class="form-group">
  	<?php $inc_rep_save_journal = get_config($dbc, 'inc_rep_save_journal'); ?>
  	<label class="col-sm-4 control-label">Save to Journal:<br /><em>Creating or updating <?= INC_REP_TILE ?> will create a note in the user's Journal.</em></label>
  	<div class="col-sm-8">
  		<label class="form-checkbox"><input type="checkbox" name="inc_rep_save_journal" value="1" <?= $inc_rep_save_journal == 1 ? 'checked' : '' ?>> Enable</label>
  	</div>
  </div>
  <div class="form-group">
  	<?php $inc_rep_rows_per_page = get_config($dbc, 'inc_rep_rows_per_page') > 0 ? get_config($dbc, 'inc_rep_rows_per_page') : 25; ?>
  	<label class="col-sm-4 control-label">Number of Rows Per Page:<br /><em>This will display the inputted number of rows per page in the <?= INC_REP_TILE ?> Dashboard.</em></label>
  	<div class="col-sm-8">
  		<input type="number" name="inc_rep_rows_per_page" value="<?= $inc_rep_rows_per_page ?>" class="form-control" min="0" step="1">
  	</div>
  </div>
</div>
<hr>

<div class="form-group">
	<h4>PDF Settings</h4>
	<?php $config_logo = $get_field_config['pdf_logo'];
	$config_header = $get_field_config['pdf_header'];
	$config_footer = $get_field_config['pdf_footer'];
	$config_notes = $get_field_config['pdf_notes'];
	$config_title = $get_field_config['pdf_title']; ?>
	<div class="form-group">
		<label for="file[]" class="col-sm-4 control-label">PDF Logo<span class="popover-examples list-inline">&nbsp;
			<a  data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
			</span>:</label>
		<div class="col-sm-8">
			<?php if($config_logo != '' && file_exists('download/'.$config_logo)) {
				echo '<a href="download/'.$config_logo.'" target="_blank">View</a>';
				?>
				<input type="hidden" name="logo_file" value="<?php echo $config_logo; ?>" />
				<input name="logo" type="file" data-filename-placement="inside" class="form-control" />
			<?php } else { ?>
				<input name="logo" type="file" data-filename-placement="inside" class="form-control" />
			<?php } ?>
		</div>
	</div>
	<div class="form-group">
		<label for="office_country" class="col-sm-4 control-label">PDF Title:</label>
		<div class="col-sm-8">
			<input name="pdf_title" class="form-control" value="<?php echo $config_title; ?>">
		</div>
	</div>
	<div class="form-group">
		<label for="office_country" class="col-sm-4 control-label">Header Info:<br><em>(e.g. - company address, phone, email, etc.)</em></label>
		<div class="col-sm-8">
			<textarea name="pdf_header" rows="3" cols="50" class="form-control"><?php echo $config_header; ?></textarea>
		</div>
	</div>
	<div class="form-group">
		<label for="office_country" class="col-sm-4 control-label">Footer Info:<br><em>(e.g. - company name, address, phone, etc.)</em></label>
		<div class="col-sm-8">
			<textarea name="pdf_footer" rows="3" cols="50" class="form-control"><?php echo $config_footer; ?></textarea>
		</div>
	</div>
	<div class="form-group">
		<label for="office_country" class="col-sm-4 control-label">PDF Notes:<br><em>Additional notes printed at the end of the pdf.</em></label>
		<div class="col-sm-8">
			<textarea name="pdf_notes" rows="5" cols="50" class="form-control"><?php echo $config_notes; ?></textarea>
		</div>
	</div>
</div>
<hr>

<div class="form-group">
	<h4>Safety Settings</h4>
	<div class="form-group">
		<label for="safety_report" class="col-sm-4 control-label">Show in Safety Report:<br><em>(Add the <?= INC_REP_TILE ?> to the Reports in the Safety tile.)</em></label>
		<div class="col-sm-8">
			<input type="checkbox" <?php if ($safety_report == 1) { echo " checked"; } ?> value="1" style="height: 20px; width: 20px;" name="safety_report">
		</div>
	</div>
</div>
<hr>

<div class="form-group">
	<h4>Tile Settings</h4>
	<div class="form-group">
		<label for="tile_name" class="col-sm-4 control-label">Tile Name:<br><i>Enter the name you would like the Incident Reports tile to be labelled as.</i></label>
		<div class="col-sm-8">
			<input type="text" name="tile_name" value="<?= INC_REP_TILE ?>" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label for="tile_name" class="col-sm-4 control-label">Tile Noun:<br><i>Enter the name you would like individual Incident Reports to be labelled as.</i></label>
		<div class="col-sm-8">
			<input type="text" name="tile_noun" value="<?= INC_REP_NOUN ?>" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label for="office_country" class="col-sm-4 control-label">Tab Options:</label>
		<div class="col-sm-8">
			<?php $tabs = explode(',',get_config($dbc, 'incident_report_tabs')); ?>
			<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary',$tabs) ? 'checked' : '' ?> name="incident_report_tabs[]" value="Summary"> Summary</label>
			<label class="form-checkbox"><input type="checkbox" <?= in_array('Admin',$tabs) ? 'checked' : '' ?> name="incident_report_tabs[]" value="Admin"> Admin Approval</label>
		</div>
	</div>
	<div class="form-group">
		<label for="office_country" class="col-sm-4 control-label">Summary Options:</label>
		<div class="col-sm-8">
			<?php $summary = explode(',',get_config($dbc, 'incident_report_summary')); ?>
			<label class="form-checkbox"><input type="checkbox" <?= in_array('Types',$summary) ? 'checked' : '' ?> name="incident_report_summary[]" value="Types"> Summary by Type</label>
			<label class="form-checkbox"><input type="checkbox" <?= in_array('Complete',$summary) ? 'checked' : '' ?> name="incident_report_summary[]" value="Complete"> Summary by Completed</label>
			<label class="form-checkbox"><input type="checkbox" <?= in_array('Staff Only',$summary) ? 'checked' : '' ?> name="incident_report_summary[]" value="Staff Only"> Staff <?= INC_REP_TILE ?> Only</label>
		</div>
	</div>
</div>