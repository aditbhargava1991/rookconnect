<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('incident_report');
error_reporting(0);

if (isset($_POST['submit'])) {
	if(!isset($_POST['incident_report'])) {
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
		//Tile Settings
	} else {
		$incident_type = $_POST['full_type'];
		$type_list = $_POST['full_type_list'];
		$incident_report = implode(',',$_POST['incident_report']);
		$report_info = filter_var(htmlentities($_POST['report_info']),FILTER_SANITIZE_STRING);
		$hide_list = [];
		foreach($_POST as $name => $value) {
			if(substr($name, 0, 11) == 'hide_field_') {
				$field = str_replace('_',' ',substr($name,11));
				foreach($value as $seclevel) {
					$hide_list[$seclevel] .= ','.$field;
				}
			}
		}
		$hide_fields = '';
		foreach($hide_list as $sec => $fields) {
			$hide_fields .= $sec.':|'.$fields.'#*#';
		}
		if(isset($_POST['keep_revisions'])) {
			$keep_revisions = 1;
		} else {
			$keep_revisions = 0;
		}
		mysqli_query($dbc, "INSERT INTO `field_config_incident_report` (`row_type`, `incident_types`) SELECT '$incident_type', '$type_list' FROM (SELECT COUNT(*) rows FROM `field_config_incident_report` WHERE `row_type`='$incident_type') num WHERE num.rows=0");
		mysqli_query($dbc, "UPDATE `field_config_incident_report` SET `incident_report`='$incident_report', `hide_fields`='$hide_fields', `report_info`='$report_info', `keep_revisions` = '$keep_revisions' WHERE `row_type`='$incident_type'");
	}

    echo '<script type="text/javascript"> window.location.replace(""); </script>';
}
?>
<script>
$(document).ready(function(){
    $("#selectall").change(function(){
      $("input[name='incident_report[]']").prop('checked', $(this).prop("checked"));
      $("input[name='incident_report_dashboard[]']").prop('checked', $(this).prop("checked"));
    });
});
</script>
</head>
<body>

<?php include ('../navigation.php');
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_incident_report WHERE row_type=''"));
$main_type = $_GET['category'];
foreach(str_getcsv(html_entity_decode($get_field_config['incident_types']), ',') as $full_type) {
	if($main_type == preg_replace('/[^a-z]/','',strtolower($full_type))) {
		$main_type = $full_type;
	}
}

$sec_level_list = get_security_levels($dbc); ?>

<div class="container">
<div class="row">
<h1><?= INC_REP_TILE ?></h1>
<div class="pad-left gap-top double-gap-bottom"><a href="incident_report.php" class="btn config-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
<input type="hidden" name="full_type" value="<?= $main_type ?>">
<input type="hidden" name="full_type_list" value="<?= $get_field_config['incident_types'] ?>">
<a href='?category=general'><button type='button' class='btn brand-btn mobile-100 mobile-block <?= empty($_GET['category']) || $_GET['category'] == 'general' ? 'active_tab' : '' ?>' >General</button></a>
<?php foreach(str_getcsv(html_entity_decode($get_field_config['incident_types']), ',') as $in_type) {
	$current_type = preg_replace('/[^a-z]/','',strtolower($in_type));
	echo "<a href='?category=".$current_type."'><button type='button' class='btn brand-btn mobile-100 mobile-block ".($_GET['category'] == $current_type ? 'active_tab' : '')."' >".$in_type."</button></a>";
} ?>
<div class="panel-group" id="accordion2">
	<?php if(isset($_GET['category']) && $_GET['category'] != 'general') { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
						Choose Fields for <?= INC_REP_TILE ?><span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_field" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $incident_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT `incident_report`, `hide_fields`, `report_info`, `keep_revisions` FROM `field_config_incident_report` WHERE `row_type`='$main_type' UNION SELECT GROUP_CONCAT(`incident_report`), '', '', '' FROM `field_config_incident_report` WHERE IFNULL(`incident_report`,'') != ''"));
					$value_config = ','.$incident_config['incident_report'].',';
					$report_info = $incident_config['report_info'];
					$hide_config = [];
					$keep_revisions = $incident_config['keep_revisions'];
					foreach(explode('#*#',$incident_config['hide_fields']) as $hide_list) {
						$hide_list = explode(':|',$hide_list);
						$hide_config[$hide_list[0]] = $hide_list[1];
					} ?>

					<div class="form-group">
						<label class="col-sm-4 control-label"><?= INC_REP_NOUN ?> Information:<br /><em>This information is specific to the type of <?= INC_REP_NOUN ?>, and will be displayed at the top of the report on the screen and on the PDF.</em></label>
						<div class="col-sm-8">
							<textarea name="report_info"><?= $report_info ?></textarea>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-4 control-label">Keep Revisions:</label>
						<div class="col-sm-8">
							<input type="checkbox" name="keep_revisions" value="1" <?= $keep_revisions == 1 ? 'checked' : '' ?> style="height: 20px; width: 20px;">
						</div>
					</div>

					<input type="checkbox" id="selectall"/> Select All
					<div id='no-more-tables'>
						<div class="form-group">
							<label class="col-sm-4 control-label">Type &amp; Individuals:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Type_DetailsLabel".',') !== FALSE) { echo " checked"; } ?> value="Type_DetailsLabel" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Use Label "Details of Staff/Member(s) Involved"
								<table border="2" cellpadding="10" class="table">
									<tr>
										<td>
											<input type="checkbox" checked readonly value="Type" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Type Of Incident<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Type[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Type,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Client".',') !== FALSE) { echo " checked"; } ?> value="Client" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Client<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Client[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Client,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Equipment<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Equipment[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Equipment,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Driver".',') !== FALSE) { echo " checked"; } ?> value="Driver" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Driver<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Driver[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Driver,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
											<input type="checkbox" <?php if (strpos($value_config, ','."Driver_WorkerLabel".',') !== FALSE) { echo " checked"; } ?> value="Driver_WorkerLabel" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Use Label "Worker/ Operator"
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Staff<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Staff[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Staff,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
											<input type="checkbox" <?php if (strpos($value_config, ','."Staff_InvolvedLabel".',') !== FALSE) { echo " checked"; } ?> value="Staff_InvolvedLabel" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Use Label "Staff Involved"
										</td>
									</tr>
									<tr>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Workers Involved".',') !== FALSE) { echo " checked"; } ?> value="Workers Involved" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Workers Involved<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Workers_Involved[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $secid => $sec_level) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Workers Involved,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Others".',') !== FALSE) { echo " checked"; } ?> value="Others" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Others Involved<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Others[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Others,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Other Driver".',') !== FALSE) { echo " checked"; } ?> value="Other Driver" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Other Driver<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Other_Driver[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Other Driver,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Individuals Witnesses".',') !== FALSE) { echo " checked"; } ?> value="Individuals Witnesses" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Witness Statements<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Individuals_Witnesses[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Individuals Witnesses,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Completed By".',') !== FALSE) { echo " checked"; } ?> value="Completed By" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Completed By<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Completed_By[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Completed By,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
									</tr>
									<tr>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Date of Happening".',') !== FALSE) { echo " checked"; } ?> value="Date of Happening" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Date of Happening<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Date_of_Happening[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Date of Happening,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Date of Report".',') !== FALSE) { echo " checked"; } ?> value="Date of Report" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Date of Report<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Date_of_Report[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Date of Report,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;<?= PROJECT_NOUN ?><br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Project[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Project,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Ticket".',') !== FALSE) { echo " checked"; } ?> value="Ticket" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;<?= TICKET_NOUN ?><br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Ticket[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Ticket,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Program".',') !== FALSE) { echo " checked"; } ?> value="Program" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Program<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Program[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Program,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
									</tr>
									<tr>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Member".',') !== FALSE) { echo " checked"; } ?> value="Member" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Member<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Member[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Member,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Description:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Description Accordion".',') !== FALSE) { echo " checked"; } ?> value="Description Accordion" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Description_Accordion[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Description Accordion,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
								<table border="2" cellpadding="10" class="table">
									<tr>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Accident Report".',') !== FALSE) { echo " checked"; } ?> value="Accident Report" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Accident Report<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Accident_Report[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $secid => $sec_level) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Accident Report,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Description Of Incident<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Description[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $secid => $sec_level) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Description,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Location".',') !== FALSE) { echo " checked"; } ?> value="Location" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Location<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Location[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $secid => $sec_level) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Location,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
											<input type="checkbox" <?php if (strpos($value_config, ','."Location Textbox".',') !== FALSE) { echo " checked"; } ?> value="Location Textbox" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Use Textbox
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Record Equipment Or Property Damage:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Record Equipment Or Property Damage".',') !== FALSE) { echo " checked"; } ?> value="Record Equipment Or Property Damage" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Record_Equipment_Or_Property_Damage[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Record Equipment Or Property Damage,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Action Taken:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Action Taken".',') !== FALSE) { echo " checked"; } ?> value="Action Taken" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Action_Taken[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Action Taken,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Record of Injury Involved:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Record Of Injury Involved".',') !== FALSE) { echo " checked"; } ?> value="Record Of Injury Involved" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Record_Of_Injury_Involved[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Record Of Injury Involved,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Determine Causes:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Determine Causes".',') !== FALSE) { echo " checked"; } ?> value="Determine Causes" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Determine_Causes[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Determine Causes,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
								<table border="2" cellpadding="10" class="table">
									<tr>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Direct Indirect Root Causes".',') !== FALSE) { echo " checked"; } ?> value="Direct Indirect Root Causes" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Direct/Indirect/Root Causes<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Direct_Indirect_Root_Causes[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Direct Indirect Root Causes,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Happening Lead Up".',') !== FALSE) { echo " checked"; } ?> value="Happening Lead Up" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Happening Lead Up<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Happening_Lead_Up[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Happening Lead Up,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Happening Follow Up".',') !== FALSE) { echo " checked"; } ?> value="Happening Follow Up" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Happening Follow Up<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Happening_Follow_Up[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Happening Follow Up,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Future Considerations".',') !== FALSE) { echo " checked"; } ?> value="Future Considerations" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Future Considerations<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Future_Considerations[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $sec_level => $secid) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Future Considerations,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Supply Pictures:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Supply Pictures".',') !== FALSE) { echo " checked"; } ?> value="Supply Pictures" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Supply_Pictures[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Supply Pictures,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
								<input type="checkbox" <?php if (strpos($value_config, ','."Pictures_ProvideLabel".',') !== FALSE) { echo " checked"; } ?> value="Pictures_ProvideLabel" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Use Label "Provide Pictures"
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Recommendations:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Recommendations".',') !== FALSE) { echo " checked"; } ?> value="Recommendations" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Recommendations[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Recommendations,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Reporting Information:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Reporting Information".',') !== FALSE) { echo " checked"; } ?> value="Reporting Information" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Reporting_Information[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Reporting Information,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
								<table border="2" cellpadding="10" class="table" style="margin-bottom: 0;">
									<tr>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Comments".',') !== FALSE) { echo " checked"; } ?> value="Comments" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Comments<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Comments[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $secid => $sec_level) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Comments,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Supervisor Statement & Signoff".',') !== FALSE) { echo " checked"; } ?> value="Supervisor Statement & Signoff" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Supervisor Statement & Signoff<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Supervisor_Statement_&_Signoff[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $secid => $sec_level) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Supervisor Statement & Signoff,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
										<td>
											<input type="checkbox" <?php if (strpos($value_config, ','."Coordinator Statement & Signoff".',') !== FALSE) { echo " checked"; } ?> value="Coordinator Statement & Signoff" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Coordinator Statement & Signoff<br />
											Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Coordinator_Statement_&_Signoff[]" multiple class="chosen-select-deselect form-group">
												<?php foreach($sec_level_list as $secid => $sec_level) {
													echo "<option ".(strpos(','.$hide_config[$secid].',',',Coordinator Statement & Signoff,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
												} ?></select>
										</td>
									</tr>
								</table>
								<input type="checkbox" <?php if (strpos($value_config, ','."Reporting Information_EditDates".',') !== FALSE) { echo " checked"; } ?> value="Reporting Information_EditDates" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Editable Signature Dates
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Funder Contact:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Funder Contact".',') !== FALSE) { echo " checked"; } ?> value="Funder Contact" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Funder_Contact[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Funder Contact,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Director Signature:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Director Signature".',') !== FALSE) { echo " checked"; } ?> value="Director Signature" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Director_Signature[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Director Signature,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
								<input type="checkbox" <?php if (strpos($value_config, ','."Director Signature_EditDates".',') !== FALSE) { echo " checked"; } ?> value="Director Signature_EditDates" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Editable Signature Dates
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Record Cause Of Accident:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Record Cause Of Accident".',') !== FALSE) { echo " checked"; } ?> value="Record Cause Of Accident" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Record_Cause_Of_Accident[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Record Cause Of Accident,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Witness Statement:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Witness Statement".',') !== FALSE) { echo " checked"; } ?> value="Witness Statement" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Witness_Statement[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Witness Statement,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
								<input type="checkbox" <?php if (strpos($value_config, ','."Witness_InterviewLabel".',') !== FALSE) { echo " checked"; } ?> value="Witness_InterviewLabel" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Use Label "Interview Witness(s) (if required)"
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Injured To Be Taken Care Of And Supervisors/Medical Aid Contacted:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Taken Care".',') !== FALSE) { echo " checked"; } ?> value="Taken Care" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Taken_Care[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Taken Care,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Initial Actions Required:</label>
							<div class="col-sm-8">
							   <input type="checkbox" <?php if (strpos($value_config, ','."Initial Actions Required".',') !== FALSE) { echo " checked"; } ?> value="Initial Actions Required" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Initial_Actions_Required[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Initial Actions Required,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Interview Witness(s):</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Interview Witness(s)".',') !== FALSE) { echo " checked"; } ?> value="Interview Witness(s)" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Interview_Witness(s)[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Interview Witness(s),') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
								<input type="checkbox" <?php if (strpos($value_config, ','."Interview_IfRequiredLabel".',') !== FALSE) { echo " checked"; } ?> value="Interview_IfRequiredLabel" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Use Label "Interview Witness(s) (if required)"
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Check Background Info:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Check Background Info".',') !== FALSE) { echo " checked"; } ?> value="Check Background Info" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Check_Background_Info[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Check Background Info,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Timing:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Timing".',') !== FALSE) { echo " checked"; } ?> value="Timing" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Timing[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Timing,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Follow Up:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up".',') !== FALSE) { echo " checked"; } ?> value="Follow Up" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Follow_Up[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Follow Up,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Corrective Action:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Corrective Action".',') !== FALSE) { echo " checked"; } ?> value="Corrective Action" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Corrective_Action[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Corrective Action,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Managers Review Signature:</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Managers Review Signature".',') !== FALSE) { echo " checked"; } ?> value="Managers Review Signature" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Managers_Review_Signature[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Managers Review Signature,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Sign Off (Multiple Signatures):</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Multiple Signatures".',') !== FALSE) { echo " checked"; } ?> value="Multiple Signatures" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Multiple_Signatures[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Multiple Signatures,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Completed By Office (Combines Follow Up, Corrective Action, and Director Signature, but only if the sections are not already turned on above.):</label>
							<div class="col-sm-8">
								<input type="checkbox" <?php if (strpos($value_config, ','."Completed By Office".',') !== FALSE) { echo " checked"; } ?> value="Completed By Office" style="height: 20px; width: 20px;" name="incident_report[]"><br />
								Hide for: <select data-placeholder="Select Security Levels" name="hide_field_Completed_By_Office[]" multiple class="chosen-select-deselect form-group">
									<?php foreach($sec_level_list as $secid => $sec_level) {
										echo "<option ".(strpos(','.$hide_config[$secid].',',',Completed By Office,') !== FALSE ? 'selected' : '')." value='$secid'>$sec_level</option>";
									} ?></select>
								<input type="checkbox" <?php if (strpos($value_config, ','."Completed By Office_EditDates".',') !== FALSE) { echo " checked"; } ?> value="Completed By Office_EditDates" style="height: 20px; width: 20px;" name="incident_report[]">&nbsp;&nbsp;Editable Signature Dates
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_dashboard" >
						Choose Fields for All <?= INC_REP_TILE ?><span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_dashboard" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $value_config = ','.$get_field_config['incident_report_dashboard'].',';
					$type_config = $get_field_config['incident_types'];
					$safety_report = $get_field_config['safety_report']; ?>
					<div id='no-more-tables'>
					<h3><?= INC_REP_NOUN ?> Dashboards</h3>
					<table border='2' cellpadding='10' class='table'>
						<tr>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;<?= PROJECT_NOUN ?>
							</td>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."Ticket".',') !== FALSE) { echo " checked"; } ?> value="Ticket" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;<?= TICKET_NOUN ?>
							</td>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."Program".',') !== FALSE) { echo " checked"; } ?> value="Program" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Program
							</td>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."Member".',') !== FALSE) { echo " checked"; } ?> value="Member" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Member
							</td>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."Client".',') !== FALSE) { echo " checked"; } ?> value="Client" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Client
							</td>
						</tr>
						<tr>
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
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."Date of Incident".',') !== FALSE) { echo " checked"; } ?> value="Date of Incident" style="height: 20px; width: 20px;" name="incident_report_dashboard[]">&nbsp;&nbsp;Date of Incident
							</td>
						</tr>
						<tr>
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
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pdf" >
						PDF Settings<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_pdf" class="panel-collapse collapse">
				<div class="panel-body">
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
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_safety" >
						Safety Settings<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_safety" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group">
						<label for="safety_report" class="col-sm-4 control-label">Show in Safety Report:<br><em>(Add the <?= INC_REP_TILE ?> to the Reports in the Safety tile.)</em></label>
						<div class="col-sm-8">
							<input type="checkbox" <?php if ($safety_report == 1) { echo " checked"; } ?> value="1" style="height: 20px; width: 20px;" name="safety_report">
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tile">
						Tile Settings<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_tile" class="panel-collapse collapse">
				<div class="panel-body">
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
				</div>
			</div>
		</div>
	<?php } ?>
</div>

<div class="form-group">
    <div class="col-sm-6">
        <a href="incident_report.php" class="btn config-btn btn-lg">Back</a>
		<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
    </div>
    <div class="col-sm-6">
        <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
    </div>
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>