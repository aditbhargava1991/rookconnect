<?php
/*
 * Copy of Site Work Orders/add_driving_log.php
 * Paths changed to Site Work Orders
 */

include('../include.php');
include_once('../phpsign/signature-to-image.php');
error_reporting(0);

if(isset($_POST['submit'])) {
	$staff = $_POST['staff'];
	$drive_date = $_POST['drive_date'];
	$drive_time = $_POST['drive_time'];
	$end_drive_time = $_POST['end_drive_time'];
	$equipment = implode(',',$_POST['equipment']);
	$comments = filter_var(htmlentities($_POST['comments']),FILTER_SANITIZE_STRING);

	if($_POST['submit'] == 'NEW') {
		$query = "INSERT INTO `site_work_driving_log` (`staff`, `drive_date`, `drive_time`, `equipment`, `comments`)
			VALUES ('$staff', '$drive_date', '$drive_time', '$equipment', '$comments')";
		$result = mysqli_query($dbc, $query);
		$log_id = mysqli_insert_id($dbc);
	} else {
		$query = "UPDATE `site_work_driving_log` SET `staff`='$staff', `end_drive_time`='$end_drive_time', `equipment`='$equipment', `comments`='$comments' WHERE `log_id`='".$_POST['submit']."'";
		$result = mysqli_query($dbc, $query);
		$log_id = $_POST['submit'];
		$final = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `site_work_driving_log` WHERE `log_id`='$log_id'"));
		$drive_date = $final['drive_date'];
		$drive_time = $final['drive_time'];
	}

	// Safety Checklist
	$inspect_date = $_POST['inspect_date'];
	$begin_odo_kms = $_POST['begin_odo_kms'];
	$final_odo_kms = $_POST['final_odo_kms'];
	$begin_hours = $_POST['begin_hours'];
	$final_hours = $_POST['final_hours'];
	$location_of_presafety = filter_var($_POST['location_of_presafety'],FILTER_SANITIZE_STRING);
	$location_of_postsafety = filter_var($_POST['location_of_postsafety'],FILTER_SANITIZE_STRING);
	$safety1 = $_POST['safety1'];
	$safety2 = $_POST['safety2'];
	$safety3 = $_POST['safety3'];
	$safety4 = $_POST['safety4'];
	$safety5 = $_POST['safety5'];
	$safety6 = $_POST['safety6'];
	$safety7 = $_POST['safety7'];
	$safety8 = $_POST['safety8'];
	$safety9 = $_POST['safety9'];
	$safety10 = $_POST['safety10'];
	$safety11 = $_POST['safety11'];
	$safety12 = $_POST['safety12'];
	$safety13 = $_POST['safety13'];
	$safety14 = $_POST['safety14'];
	$safety15 = $_POST['safety15'];
	$safety16 = $_POST['safety16'];
	$safety17 = $_POST['safety17'];
	$safety18 = $_POST['safety18'];
	$safety19 = $_POST['safety19'];
	$safety20 = $_POST['safety20'];
	$safety21 = $_POST['safety21'];
	$safety22 = $_POST['safety22'];
	$safety23 = $_POST['safety23'];
	$safety24 = $_POST['safety24'];
	$safety25 = $_POST['safety25'];
	$safety26 = $_POST['safety26'];
	$safety27 = $_POST['safety27'];
	$safety28 = $_POST['safety28'];
	$safety29 = $_POST['safety29'];
	$safety30 = $_POST['safety30'];
	$safety31 = $_POST['safety31'];
	$safety32 = $_POST['safety32'];
	$safety33 = $_POST['safety33'];
	$safety34 = $_POST['safety34'];
	$safety35 = $_POST['safety35'];
	$safety36 = $_POST['safety36'];
	$safety37 = $_POST['safety37'];
	$safety38 = $_POST['safety38'];
	$repair_note = filter_var(htmlentities($_POST['repair_note']),FILTER_SANITIZE_STRING);

	if($_POST['safetyinspectid'] == 'PRE') {
		$query = "INSERT INTO `site_work_driving_inspect` (`drivinglogid`, `inspect_date`, `begin_odo_kms`, `final_odo_kms`, `safety1`, `safety2`, `safety3`, `safety4`, `safety5`, `safety6`, `safety7`, `safety8`, `safety9`, `safety10`, `safety11`, `safety12`, `safety13`, `safety14`, `safety15`, `safety16`, `safety17`, `safety18`, `safety19`, `safety20`, `safety21`, `safety22`, `safety23`, `safety24`, `safety25`, `safety26`, `safety27`, `safety28`, `safety29`, `safety30`, `safety31`, `safety32`, `safety33`, `safety34`, `safety35`, `safety36`, `safety37`, `safety38`, `repair_note`, `location_of_postsafety`, `location_of_presafety`)
			VALUES ('$log_id', '$inspect_date', '$begin_odo_kms', '$final_odo_kms', '$safety1', '$safety2', '$safety3', '$safety4', '$safety5', '$safety6', '$safety7', '$safety8', '$safety9', '$safety10', '$safety11', '$safety12', '$safety13', '$safety14', '$safety15', '$safety16', '$safety17', '$safety18', '$safety19', '$safety20', '$safety21', '$safety22', '$safety23', '$safety24', '$safety25', '$safety26', '$safety27', '$safety28', '$safety29', '$safety30', '$safety31', '$safety32', '$safety33', '$safety34', '$safety35', '$safety36', '$safety37', '$safety38', '$repair_note', '$location_of_postsafety', '$location_of_presafety')";
		$result = mysqli_query($dbc, $query);
		$inspect_id = mysqli_insert_id($dbc);
	} else {
		$inspect_id = $_POST['safetyinspectid'];
		$query = "UPDATE `site_work_driving_inspect` SET `drivinglogid`='$log_id', `inspect_date`='$inspect_date', `begin_odo_kms`='$begin_odo_kms', `final_odo_kms`='$final_odo_kms', `safety1`='$safety1', `safety2`='$safety2', `safety3`='$safety3', `safety4`='$safety4', `safety5`='$safety5', `safety6`='$safety6', `safety7`='$safety7', `safety8`='$safety8', `safety9`='$safety9', `safety10`='$safety10', `safety11`='$safety11', `safety12`='$safety12', `safety13`='$safety13', `safety14`='$safety14', `safety15`='$safety15', `safety16`='$safety16', `safety17`='$safety17', `safety18`='$safety18', `safety19`='$safety19', `safety20`='$safety20', `safety21`='$safety21', `safety22`='$safety22', `safety23`='$safety23', `safety24`='$safety24', `safety25`='$safety25', `safety26`='$safety26', `safety27`='$safety27', `safety28`='$safety28', `safety29`='$safety29', `safety30`='$safety30', `safety31`='$safety31', `safety32`='$safety32', `safety33`='$safety33', `safety34`='$safety34', `safety35`='$safety35', `safety36`='$safety36', `safety37`='$safety37', `safety38`='$safety38', `repair_note`='$repair_note', `location_of_postsafety`='$location_of_postsafety', `location_of_presafety`='$location_of_presafety' WHERE `safetyinspectid`='$inspect_id'";
		$result = mysqli_query($dbc, $query);
	}

	$pre_sign = $_POST['pre_sign'];
	if(!empty($pre_sign)) {
		$img = sigJsonToImage($pre_sign);
		imagepng($img, '../Site Work Orders/download/pre_sign_'.$log_id.'.png');
		mysqli_query($dbc, "UPDATE `site_work_driving_inspect` SET `pre_sign`='$pre_sign' WHERE `safetyinspectid`='$inspect_id'");
	}
	$post_sign = $_POST['post_sign'];
	if(!empty($post_sign)) {
		$img = sigJsonToImage($post_sign);
		imagepng($img, '../Site Work Orders/download/post_sign_'.$log_id.'.png');
		mysqli_query($dbc, "UPDATE `site_work_driving_inspect` SET `post_sign`='$post_sign' WHERE `safetyinspectid`='$inspect_id'");
	}

	$pdf_name = "driving_log_".$log_id.".pdf";
	$html = "<h1>Driving Log</h1>";
	$html .= '<table width="100%" cellspacing="2"><tr><td width="25%">Staff:</td>';
	$html .= "<td>".get_contact($dbc, $staff)."</td></tr>";
	$html .= "<tr><td>Log Date:</td>";
	$html .= "<td>$drive_date</td></tr>";
	$html .= "<tr><td>Equipment:</td>";
	$html .= "<td>";
    $equip_hr_km = false;
	foreach($_POST['equipment'] as $id) {
		$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='$id'"));
		$html .= "Unit #".$equipment['unit_number'].': '.(empty($equipment['model']) ? '' : $equipment['model'].': ').$equipment['label']."<br />";
        if($id > 0 $final_hours > 0 && $final_odo_kms > 0 && !$equip_hr_km) {
            mysqli_query("UPDATE `equipment` SET `mileage`='$final_odo_kms', `hours_operated`='$final_hours' WHERE `equipmentid`='$id'");
            $equip_hr_km = true;
        }
	}
	$html .= "</td></tr>";
	$equipment = implode(',',$_POST['equipment']);
	if($_POST['safetyinspectid'] == 'PRE') {
		$html .= "<tr><td>Log Start Time:</td>";
		$html .= "<td>$drive_time</td></tr>";
	} else {
		$html .= "<tr><td>Logged Time:</td>";
		$html .= "<td 	>$drive_time - $end_drive_time</td></tr>";
	}
	if($comments != '') {
		$html .= "<tr><td>Log Comments:</td>";
		$html .= "<td>".html_entity_decode($comments)."</td></tr>";
	}
	if($safety29 == 'Yes') {
		$safety29 = 'No Problems';
	} else if($safety29 == 'No') {
		$safety29 = 'Problems';
	}
	if($safety30 == 'Yes') {
		$safety30 = 'No Problems';
	} else if($safety30 == 'No') {
		$safety30 = 'Problems';
	}
	if($safety31 == 'Yes') {
		$safety31 = 'No Problems';
	} else if($safety31 == 'No') {
		$safety31 = 'Problems';
	}
	if($safety32 == 'Yes') {
		$safety32 = 'No Problems';
	} else if($safety32 == 'No') {
		$safety32 = 'Problems';
	}
	$html .= '</table>';
	$html .= "<h2>Driving Safety Checklist</h2>";
	$html .= '<table width="100%" cellspacing="2">
		<tr>
			<td width="60%">Inspection Location:</td>
			<td width="20%">'.$location_of_presafety.'</td>
			<td width="20%">'.$location_of_postsafety.'</td>
		</tr>
		<tr>
			<td>Odometer Kilometers:</td>
			<td>'.$begin_odo_kms.'</td>
			<td>'.('pre' == $checklist ? '---' : $final_odo_kms).'</td>
		</tr>
		<tr>
			<td>Operating Hours:</td>
			<td>'.$begin_hours.'</td>
			<td>'.('pre' == $checklist ? '---' : $final_hours).'</td>
		</tr>
		<tr>
			<td>General Checklist</td>
			<td>Pre-Trip Status</td>
			<td>'.($_POST['include_post_trip'] == 1 ? 'Post-Trip Status' : '').'</td>
		</tr>
		<tr>
			<td>ENGINE OIL WITHIN ACCEPTABLE LIMITS:</td>
			<td>'.$safety1.'</td>
			<td>'.$safety2.'</td>
		</tr>
		<tr>
			<td>TIRE TREAD AND SIDEWALLS SHOW NO DAMAGE:</td>
			<td>'.$safety3.'</td>
			<td>'.$safety4.'</td>
		</tr>
		<tr>
			<td>TIRE BOLTS CHECKED (HAND TIGHT):</td>
			<td>'.$safety5.'</td>
			<td>'.$safety6.'</td>
		</tr>
		<tr>
			<td>TIRE INFLATION:</td>
			<td>'.$safety7.'</td>
			<td>'.$safety8.'</td>
		</tr>
		<tr>
			<td>WINDOWS CLEAN INSIDE AND OUTSIDE:</td>
			<td>'.$safety9.'</td>
			<td>'.$safety10.'</td>
		</tr>
		<tr>
			<td>HITCH AND PINS (GOOSENECK OR DROP HITCH):</td>
			<td>'.$safety11.'</td>
			<td>'.$safety12.'</td>
		</tr>
		<tr>
			<td>HORN:</td>
			<td>'.$safety13.'</td>
			<td>'.$safety14.'</td>
		</tr>
		<tr>
			<td>EMERGENCY / INCIDENT REPORTING KITS AVAILABLE:</td>
			<td>'.$safety15.'</td>
			<td>'.$safety16.'</td>
		</tr>
		<tr>
			<td>FIRE EXTINGUISHER AVAILABLE:</td>
			<td>'.$safety17.'</td>
			<td>'.$safety18.'</td>
		</tr>
		<tr>
			<td>Engine On Criteria Checklist</td>
			<td>Pre-Trip Status</td>
			<td>'.($_POST['include_post_trip'] == 1 ? 'Post-Trip Status' : '').'</td>
		</tr>
		<tr>
			<td>HEADLIGHTS FUNCTION ON BOTH HI AND LO BEAM:</td>
			<td>'.$safety19.'</td>
			<td>'.$safety20.'</td>
		</tr>
		<tr>
			<td>TURN SIGNALS FUNCTION:</td>
			<td>'.$safety21.'</td>
			<td>'.$safety22.'</td>
		</tr>
		<tr>
			<td>BRAKE LIGHTS FUNCTION INCLUDING TRAILER APPLICABLE:</td>
			<td>'.$safety23.'</td>
			<td>'.$safety24.'</td>
		</tr>
		<tr>
			<td>MIRRORS FUNCTION AND ARE CLEAN:</td>
			<td>'.$safety25.'</td>
			<td>'.$safety26.'</td>
		</tr>
		<tr>
			<td>STEERING MECHANISM AND FLUID:</td>
			<td>'.$safety27.'</td>
			<td>'.$safety28.'</td>
		</tr>
		<tr>
			<td>FLUID LEAKS:</td>
			<td>'.$safety29.'</td>
			<td>'.$safety30.'</td>
		</tr>
		<tr>
			<td>ANY NEW DAMAGE NOTED PRIOR TO USING THIS VEHICLE:</td>
			<td>'.$safety31.'</td>
			<td>'.$safety32.'</td>
		</tr>
		<tr>
			<td>General Checklist</td>
			<td>Pre-Trip Status</td>
			<td>'.($_POST['include_post_trip'] == 1 ? 'Post-Trip Status' : '').'</td>
		</tr>
		<tr>
			<td>ALL TOOLS PRESENT AND FUNCTIONING PROPERLY:</td>
			<td>'.$safety33.'</td>
			<td>'.$safety34.'</td>
		</tr>
		<tr>
			<td>TRUCK CLEAN INSIDE AND OUTSIDE:</td>
			<td>'.$safety35.'</td>
			<td>'.$safety36.'</td>
		</tr>
		<tr>
			<td>TRUCK FILLED WITH GAS:</td>
			<td>'.$safety37.'</td>
			<td>'.$safety38.'</td>
		</tr>
		</table>
		<h2>Driving Log Sign Off</h2>
		<table width="100%" cellspacing="5">
		<tr>
			<td width="35%"><b>If anything needs repairing, please specify:</b></td>
			<td width="70%">'.(empty($repair_note) ? 'N/A' : html_entity_decode($repair_note)).'</td>
		</tr>
		<tr>
			<td colspan="2">I performed an inspection of the vehicle noted above using the criteria set out in Schedule 1 of Part 2, NSC Standard 13 and as per sections 10(4) and 10(10) of Alberta’s Commercial Vehicle Safety Regulation, (AR 121/2009) and report the above.</td>
		</tr>
		<tr>
			<td colspan="2">I have personally inspected the vehicle above and have found it to be in the condition listed above.</td>
		</tr>
		<tr>
			<td>Pre-Trip Signature:</td>
			<td><img width="190" src="../Site Work Orders/download/pre_sign_'.$log_id.'.png"></td>
		</tr>
		<tr>
			<td>Post-Trip Signature:</td>
			<td>'.($_POST['safetyinspectid'] == 'PRE' || $_POST['include_post_trip'] != '1' ? '' : '<img width="190" src="../Site Work Orders/download/post_sign_'.$log_id.'.png">').'</td>
		</tr></table>';
	include('../tcpdf/tcpdf.php');

    class MYPDF extends TCPDF {

        //Page header
        public function Header() {
        }

        // Page footer
        public function Footer() {
            // Position at 15 mm from bottom
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $footer_text = '<p style="text-align:right;">'.$this->getAliasNumPage().'</p>';
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);

	$pdf->AddPage();
	$pdf->SetFont('helvetica', '', 8);
	$pdf->setCellHeightRatio(1);
	$pdf->writeHTML($html, true, false, true, false, '');

	if(!file_exists('../Site Work Orders/download')) {
		mkdir('../Site Work Orders/download', 0777, true);
	}
	$pdf->Output('../Site Work Orders/download/'.$pdf_name, 'F');

	echo "<script>window.location.replace('driving_log.php');</script>";
}

include_once ('../navigation.php');
checkAuthorised('safety');

$log_id = '';
$staff = $_SESSION['contactid'];
$drive_date = date('Y-m-d');
$equipment = '';
$drive_time = date('h:i a');
$end_drive_time = date('h:i a');
$comments = '';
if(!empty($_GET['log_id'])) {
	$log_id = filter_var($_GET['log_id'],FILTER_SANITIZE_STRING);
	$driving_log = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `site_work_driving_log` WHERE `log_id`='$log_id'"));
	$staff = $driving_log['staff'];
	$drive_date = $driving_log['drive_date'];
	$equipment = explode(',',$driving_log['equipment']);
	$drive_time = $driving_log['drive_time'];
	$end_drive_time = (!empty($driving_log['end_drive_time']) ? $driving_log['end_drive_time'] : date('h:i a') );
	$comments = $driving_log['comments'];
} ?>
<div class="container">
	<div class="row">
		<h1><?= (!empty($_GET['log_id']) ? 'Edit Driving Log for '.get_contact($dbc, $staff).' from '.$drive_date : 'Add A New Driving Log for '.get_contact($dbc, $staff)) ?></h1>

		<div class="form-group">
			<div class="col-sm-6">
				<a href="driving_log.php" class="btn config-btn">Back to Dashboard</a>
			</div>
			<div class="clearfix"></div>
		</div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
		<div class="panel-group" id="accordion2">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_log" >
							Driving Log: <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_log" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group"><label class="col-sm-4 control-label">Driver:</label>
							<div class="col-sm-8"><select name="staff" data-placeholder="Select a Driver" class="form-control chosen-select-deselect"><option></option>
								<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0 AND `show_hide_user`=1"),MYSQLI_ASSOC));
								foreach($staff_list as $id) {
									echo "<option ".($id == $staff ? 'selected' : '')." value='$id'>".get_contact($dbc, $id)."</option>";
								} ?>
								</select>
							</div>
						</div>
						<?php if(empty($_GET['log_id'])) { ?>
							<div class="form-group"><label class="col-sm-4 control-label">Driving Date:</label>
								<div class="col-sm-8">
									<input type="text" name="drive_date" class="form-control datepicker" value="<?= $drive_date ?>">
								</div>
							</div>
							<div class="form-group"><label class="col-sm-4 control-label">Start Driving Time:</label>
								<div class="col-sm-8">
									<input type="text" name="drive_time" class="form-control datetimepicker" value="<?= $drive_time ?>">
								</div>
							</div>
						<?php } else { ?>
							<div class="form-group"><label class="col-sm-4 control-label">End Driving Time:</label>
								<div class="col-sm-8">
									<input type="text" name="end_drive_time" class="form-control datetimepicker" value="<?= $end_drive_time ?>">
								</div>
							</div>
						<?php } ?>
						<?php $site_log_equip_cat_list = explode(',',get_config($dbc, 'site_log_equip_cat'));
						foreach($site_log_equip_cat_list as $site_log_equip_cat) { ?>
							<div class="form-group"><label class="col-sm-4 control-label"><?= (empty($site_log_equip_cat) ? 'Equipment' : $site_log_equip_cat) ?>:</label>
								<div class="col-sm-8">
									<select name="equipment[]" multiple data-placeholder="Select <?= (empty($site_log_equip_cat) ? 'Equipment' : $site_log_equip_cat) ?>" class="form-control chosen-select-deselect">
										<?php
										$equip_list = mysqli_query($dbc, "SELECT `category`, `type`, `unit_number`, `make`, `model`, `label`, `equipmentid`, `mileage`, `operating_hours` FROM `equipment` WHERE (`category`='$site_log_equip_cat' OR '$site_log_equip_cat'='') AND `deleted` = 0 ORDER BY `category`, `type`, `unit_number`, `make`, `model`, `equipmentid`");
										$category = '';
										while($equip_row = mysqli_fetch_array($equip_list)) {
											if($category != $equip_row['category']) {
												echo ($category != '' ? '</optgroup>' : '')."<optgroup label='".$equip_row['category']."' />";
												$category = $equip_row['category'];
											}
											echo "<option data-category='".$equip_row['category']."' data-mileage='".$equip_row['mileage']."' data-hours='".$equip_row['operating_hours']."' ".(in_array($equip_row['equipmentid'],$equipment) ? 'selected' : '')." value='".$equip_row['equipmentid']."'>Unit #".$equip_row['unit_number'].': '.(empty($equip_row['model']) ? '' : $equip_row['model'].': ').$equip_row['label']."</option>";
										} ?>
									</select>
								</div>
							</div>
						<?php } ?>
						<div class="form-group"><label class="col-sm-4 control-label">Log Comments:</label>
							<div class="col-sm-8">
								<textarea name="comments" class="form-control"><?= $comments ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_safety" >
							Driving Safety Checklist: <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_safety" class="panel-collapse collapse">
					<div class="panel-body">
						<?php $checklist = mysqli_query($dbc, "SELECT * FROM `site_work_driving_inspect` WHERE `drivinglogid`='$log_id'");

						$inspect_date = date('Y-m-d');
						$begin_odo_kms = 0;
						$final_odo_kms = 0;
						$begin_hours = 0;
						$final_hours = 0;
						$location_of_presafety = '';
						$location_of_postsafety = '';
						$safety1 = '';
						$safety2 = '';
						$safety3 = '';
						$safety4 = '';
						$safety5 = '';
						$safety6 = '';
						$safety7 = '';
						$safety8 = '';
						$safety9 = '';
						$safety10 = '';
						$safety11 = '';
						$safety12 = '';
						$safety13 = '';
						$safety14 = '';
						$safety15 = '';
						$safety16 = '';
						$safety17 = '';
						$safety18 = '';
						$safety19 = '';
						$safety20 = '';
						$safety21 = '';
						$safety22 = '';
						$safety23 = '';
						$safety24 = '';
						$safety25 = '';
						$safety26 = '';
						$safety27 = '';
						$safety28 = '';
						$safety29 = '';
						$safety30 = '';
						$safety31 = '';
						$safety32 = '';
						$safety33 = '';
						$safety34 = '';
						$safety35 = '';
						$safety36 = '';
						$safety37 = '';
						$safety38 = '';
						$repair_note = '';

						if(mysqli_num_rows($checklist) > 0) {
							$checklist_details = mysqli_fetch_array($checklist);
							$inspect_date = $checklist_details['inspect_date'];
							$begin_odo_kms = $checklist_details['begin_odo_kms'];
							$final_odo_kms = $checklist_details['final_odo_kms'];
							$begin_hours = $checklist_details['begin_hours'];
							$final_hours = $checklist_details['final_hours'];
							$location_of_presafety = $checklist_details['location_of_presafety'];
							$location_of_postsafety = $checklist_details['location_of_postsafety'];
							$safety1 = $checklist_details['safety1'];
							$safety2 = $checklist_details['safety2'];
							$safety3 = $checklist_details['safety3'];
							$safety4 = $checklist_details['safety4'];
							$safety5 = $checklist_details['safety5'];
							$safety6 = $checklist_details['safety6'];
							$safety7 = $checklist_details['safety7'];
							$safety8 = $checklist_details['safety8'];
							$safety9 = $checklist_details['safety9'];
							$safety10 = $checklist_details['safety10'];
							$safety11 = $checklist_details['safety11'];
							$safety12 = $checklist_details['safety12'];
							$safety13 = $checklist_details['safety13'];
							$safety14 = $checklist_details['safety14'];
							$safety15 = $checklist_details['safety15'];
							$safety16 = $checklist_details['safety16'];
							$safety17 = $checklist_details['safety17'];
							$safety18 = $checklist_details['safety18'];
							$safety19 = $checklist_details['safety19'];
							$safety20 = $checklist_details['safety20'];
							$safety21 = $checklist_details['safety21'];
							$safety22 = $checklist_details['safety22'];
							$safety23 = $checklist_details['safety23'];
							$safety24 = $checklist_details['safety24'];
							$safety25 = $checklist_details['safety25'];
							$safety26 = $checklist_details['safety26'];
							$safety27 = $checklist_details['safety27'];
							$safety28 = $checklist_details['safety28'];
							$safety29 = $checklist_details['safety29'];
							$safety30 = $checklist_details['safety30'];
							$safety31 = $checklist_details['safety31'];
							$safety32 = $checklist_details['safety32'];
							$safety33 = $checklist_details['safety33'];
							$safety34 = $checklist_details['safety34'];
							$safety35 = $checklist_details['safety35'];
							$safety36 = $checklist_details['safety36'];
							$safety37 = $checklist_details['safety37'];
							$safety38 = $checklist_details['safety38'];
							$repair_note = $checklist_details['repair_note'];
							$checklist = (empty($checklist_details['post_sign']) ? 'post' : 'final'); ?>
							<h4>Post Driving Safety Checklist</h4>
							<script>
							function trip_done(checked) {
								if(checked) {
									$('input[type=radio][value=Yes]').not(':disabled,[readonly]').prop('checked','checked');
								}
							}
							function add_post_trip() {
								$('#post_trip_done_chk').show();
								$('.posttrip').show();
								$('.posttrip').find('input').prop('disabled', false);
								$('.pretrip').removeClass('col-sm-6').addClass('col-sm-3');
								$('[name="include_post_trip"]').val('1');
							}
							$(document).ready(function() {
								$('input[type=radio][value=No],input[type=radio][value=Repair]').change(function() {
									if(this.checked) {
										$('[name=select_all]').removeAttr('checked');
									}
								});
								$('.posttrip').hide();
								$('.posttrip').find('input').prop('disabled', true);
								$('#post_trip_done_chk').hide();
								$('.pretrip').removeClass('col-sm-3').addClass('col-sm-6');
							});
							</script>
							<input type="hidden" name="safetyinspectid" value="<?= $checklist_details['safetyinspectid'] ?>">
							<input type="hidden" name="include_post_trip" value="0">
							<label id="post_trip_done_chk" class="pull-right"><input type="checkbox" name="select_all" onchange="trip_done(this.checked);"> Post Trip Done</label>
							<!-- <button class="btn brand-btn pull-right" onclick="$('[name=select_all]').prop('checked','checked').change(); return false;">Select All</button> -->
							<button class="btn brand-btn pull-right" onclick="add_post_trip(); return false;">Add Post Trip</button>
							<div class="clearfix"></div>
						<?php } else {
							$checklist = 'pre'; ?>
							<h4>Pre Driving Safety Checklist</h4>
							<input type="hidden" name="safetyinspectid" value="PRE">
						<?php } ?>
						<input type="hidden" name="inspect_date" value="<?= $inspect_date ?>">
						<div class="clearfix hide-titles-mob">
							<label class="col-sm-6 control-label">General Checklist</label>
							<label class="col-sm-3 text-center pretrip">Pre-Trip Status</label>
							<label class="col-sm-3 text-center posttrip">Post-Trip Status</label>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">Inspection Location:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<input type="text" name="location_of_presafety" value="<?= $location_of_presafety ?>" class="form-control" <?= ('pre' == $checklist ? '' : 'readonly tabindex="-1"') ?>>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="show-on-mob">Post-Trip Status</label>
								<input type="text" name="location_of_postsafety" value="<?= $location_of_postsafety ?>" class="form-control" <?= ('post' == $checklist ? '' : 'readonly tabindex="-1"') ?>>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">Odometer Kilometers:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<input type="number" name="begin_odo_kms" value="<?= $begin_odo_kms ?>" class="form-control" <?= ('pre' == $checklist ? '' : 'readonly tabindex="-1"') ?>>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="show-on-mob">Post-Trip Status</label>
								<input type="number" name="final_odo_kms" value="<?= $final_odo_kms ?>" class="form-control" <?= ('post' == $checklist ? '' : 'readonly tabindex="-1"') ?>>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">Operating Hours:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<input type="number" name="begin_hours" value="<?= $begin_hours ?>" class="form-control" <?= ('pre' == $checklist ? '' : 'readonly tabindex="-1"') ?>>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="show-on-mob">Post-Trip Status</label>
								<input type="number" name="final_hours" value="<?= $final_hours ?>" class="form-control" <?= ('post' == $checklist ? '' : 'readonly tabindex="-1"') ?>>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">ENGINE OIL WITHIN ACCEPTABLE LIMITS:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety1" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety1 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety1" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety1 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety1" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety1 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety2" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety2 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety2" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety2 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety2" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety2 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">TIRE TREAD AND SIDEWALLS SHOW NO DAMAGE:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety3" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety3 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety3" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety3 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety3" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety3 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety4" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety4 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety4" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety4 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety4" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety4 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">TIRE BOLTS CHECKED (HAND TIGHT):</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety5" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety5 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety5" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety5 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety5" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety5 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety6" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety6 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety6" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety6 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety6" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety6 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">TIRE INFLATION:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety7" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety7 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety7" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety7 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety7" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety7 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety8" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety8 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety8" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety8 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety8" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety8 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">WINDOWS CLEAN INSIDE AND OUTSIDE:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety9" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety9 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety9" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety9 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety9" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety9 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety10" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety10 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety10" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety10 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety10" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety10 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">HITCH AND PINS (GOOSENECK OR DROP HITCH):</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety11" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety11 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety11" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety11 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety11" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety11 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety12" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety12 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety12" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety12 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety12" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety12 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">HORN:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety13" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety13 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety13" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety13 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety13" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety13 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety14" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety14 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety14" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety14 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety14" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety14 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">EMERGENCY / INCIDENT REPORTING KITS AVAILABLE:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety15" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety15 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety15" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety15 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety15" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety15 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety16" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety16 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety16" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety16 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety16" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety16 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">FIRE EXTINGUISHER AVAILABLE:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety17" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety17 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety17" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety17 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety17" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety17 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety18" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety18 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety18" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety18 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety18" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety18 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>

						<div class="clearfix hide-titles-mob">
							<label class="col-sm-6 control-label">Engine On Criteria Checklist</label>
							<label class="col-sm-3 text-center pretrip">Pre-Trip Status</label>
							<label class="col-sm-3 text-center posttrip">Post-Trip Status</label>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">HEADLIGHTS FUNCTION ON BOTH HI AND LO BEAM:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety19" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety19 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety19" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety19 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety19" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety19 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety20" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety20 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety20" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety20 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety20" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety20 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">TURN SIGNALS FUNCTION:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety21" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety21 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety21" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety21 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety21" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety21 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety22" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety22 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety22" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety22 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety22" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety22 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">BRAKE LIGHTS FUNCTION INCLUDING TRAILER APPLICABLE:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety23" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety23 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety23" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety23 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety23" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety23 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety24" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety24 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety24" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety24 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety24" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety24 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">MIRRORS FUNCTION AND ARE CLEAN:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety25" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety25 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety25" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety25 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety25" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety25 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety26" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety26 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety26" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety26 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety26" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety26 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">STEERING MECHANISM AND FLUID:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety27" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety27 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety27" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety27 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety27" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety27 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety28" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety28 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety28" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety28 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety28" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety28 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">FLUID LEAKS:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety29" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety29 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> No Problems</label>
								<label><input type="radio" name="safety29" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety29 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> Problems</label>
								<label><input type="radio" name="safety29" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety29 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety30" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety30 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> No Problems</label>
								<label><input type="radio" name="safety30" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety30 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> Problems</label>
								<label><input type="radio" name="safety30" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety30 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">ANY NEW DAMAGE NOTED PRIOR TO USING THIS VEHICLE:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety31" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety31 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> No Problems</label>
								<label><input type="radio" name="safety31" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety31 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> Problems</label>
								<label><input type="radio" name="safety31" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety31 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety32" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety32 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> No Problems</label>
								<label><input type="radio" name="safety32" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety32 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> Problems</label>
								<label><input type="radio" name="safety32" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety32 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>

						<div class="clearfix hide-titles-mob">
							<label class="col-sm-6 control-label">General Checklist</label>
							<label class="col-sm-3 text-center pretrip">Pre-Trip Status</label>
							<label class="col-sm-3 text-center posttrip">Post-Trip Status</label>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">ALL TOOLS PRESENT AND FUNCTIONING PROPERLY:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety33" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety33 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety33" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety33 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety33" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety33 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety34" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety34 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety34" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety34 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety34" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety34 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">TRUCK CLEAN INSIDE AND OUTSIDE:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety35" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety35 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety35" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety35 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety35" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety35 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety36" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety36 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety36" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety36 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety36" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety36 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label">TRUCK FILLED WITH GAS:</label>
							<div class="col-sm-3 text-center pretrip">
								<label class="pretrip show-on-mob">Pre-Trip Status</label>
								<label><input type="radio" name="safety37" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety37 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety37" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety37 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety37" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('pre' == $checklist ? '' : ($safety37 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							</div>
							<div class="col-sm-3 text-center posttrip"><?php if('pre' == $checklist) { echo '---'; } else { ?>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label class="posttrip show-on-mob">Post-Trip Status</label>
								<label><input type="radio" name="safety38" value="Yes" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety38 == 'Yes' ? ' checked tabindex="-1"' : 'disabled')) ?>> Yes</label>
								<label><input type="radio" name="safety38" value="No" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety38 == 'No' ? ' checked tabindex="-1"' : 'disabled')) ?>> No</label>
								<label><input type="radio" name="safety38" value="Repair" style="height:1.5em;margin:0.5em;width:1.5em;" <?= ('post' == $checklist ? '' : ($safety38 == 'Repair' ? ' checked tabindex="-1"' : 'disabled')) ?>> Repair Requested</label>
							<?php } ?></div>
						</div>
						<div class="form-group">
							<div class="col-sm-12"><?php if('final' != $checklist) { ?>
									<label class=""><input type="checkbox" name="agree_inspect" style="height:1.5em; width:1.5em;"> I performed an inspection of the vehicle noted above using the criteria set out in Schedule 1 of Part 2, NSC Standard 13 and as per sections 10(4) and 10(10) of Alberta’s Commercial Vehicle Safety Regulation, (AR 121/2009) and report the following.<span class="brand-color">*</span></label>
								<?php } ?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">If anything needs repairing, please specify:</label>
							<div class="col-sm-8"><?php if('final' == $checklist) {
									echo html_entity_decode($repair_note);
								} else { ?>
									<textarea name="repair_note" class="form-control"><?= html_entity_decode($repair_note) ?></textarea>
								<?php } ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12"><?php if('final' != $checklist) { ?>
									<label class=""><input type="checkbox" name="agree_condition" style="height:1.5em; width:1.5em;"> I have personally inspected the vehicle above and have found it to be in the condition listed above, click the box to accept these terms.<span class="brand-color">*</span></label>
								<?php } ?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Pre-Trip Signature:</label>
							<div class="col-sm-8"><?php if('pre' == $checklist) {
									$output_name = 'pre_sign';
									include('../phpsign/sign_multiple.php');
								} else { ?>
									<img src="../Site Work Orders/download/pre_sign_<?= $log_id ?>.png">
								<?php } ?>
							</div>
						</div>
						<div class="form-group posttrip">
							<label class="col-sm-4 control-label">Post-Trip Signature:</label>
							<div class="col-sm-8"><?php if('post' == $checklist) {
									$output_name = 'post_sign';
									include('../phpsign/sign_multiple.php');
								} else if('final' == $checklist) { ?>
									<img src="../Site Work Orders/download/post_sign_<?= $log_id ?>.png">
								<?php } else {
									echo "---";
								} ?>
							</div>
						</div>
						<?php if($checklist != 'final') { ?>
							<script>
							$(document).ready(function() {
								$('form').submit(function() {
									if($('[name="equipment[]"]').val() == null) {
										alert('Please select the equipment.');
										return false;
									}
									if($('form input.form-control:enabled').filter(function() { return this.value == ''; }).length > 0) {
										alert('Please fill in the location and odometer reading.');
										return false;
									}
									var all_checked = true;
									$('input[type=radio]:enabled').each(function() {
										if($('[name='+this.name+']:checked').val() == undefined) {
											all_checked = false;
										}
									});
									if(!all_checked) {
										alert('Please select a status for all items on the checklist.');
										return false;
									}
									if(!$('[name=agree_inspect]')[0].checked || !$('[name=agree_condition]')[0].checked) {
										alert('Please agree to the conditions.');
										return false;
									}
								});
							});
							</script>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group double-gap-top">
			<p><span class="brand-color"><em>Required Fields *</em></span></p>
		</div>

		<div class="form-group">
			<div class="col-sm-6">
				<a href="driving_log.php" class="btn brand-btn btn-lg">Back</a>
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="submit"	value="<?= (empty($log_id) ? 'NEW' : $log_id) ?>" class="btn brand-btn btn-lg pull-right">Submit</button>
			</div>
			<div class="clearfix"></div>
		</div>

		</form>
	</div>
</div>
<?php include('../footer.php'); ?>