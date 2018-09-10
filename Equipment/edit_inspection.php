<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');
include('../phpsign/signature-to-image.php');

if (isset($_POST['submit'])) {
	$staff = $_POST['staffid'];
	$type = $_POST['type'];
	$datetime = date('Y-m-d H:i:s', strtotime($_POST['date'].' '.$_POST['time']));
	$timer = $_POST['timer'];
	$equipmentid = $_POST['unit'];
	// $good = [];
	// $attention = [];
	$good = '';
	$attention = '';
	$incomplete = [];
	$inspection_checklist = [];
	foreach($_POST['checklist_item'] as $i => $value) {
		$status = $_POST['checklist'][$i];
		$details = htmlentities($_POST['checklist_detail'][$i]);
		$checklist_row = '';
		if (isset($status)) {
			$checklist_row = $value.'**#**'.$_POST['checklist'][$i];
			if (isset($_POST['checklist_details'][$i])) {
				$checklist_row .= '**#**'.$_POST['checklist_details'][$i];
			}
			$inspection_checklist[] = $checklist_row;
		} else {
			$incomplete[] = [$value,$details];
		}
	}
	// $good_items = [];
	// foreach($good as $val) {
	// 	$good_items[] = implode('*#*',$val);
	// }
	// $good_items = filter_var(implode('#*#',$good_items),FILTER_SANITIZE_STRING);
	// $attention_needed = [];
	// foreach($attention as $val) {
	// 	$attention_needed[] = implode('*#*',$val);
	// }
	// $attention_needed = filter_var(implode('#*#',$attention_needed),FILTER_SANITIZE_STRING);
	$inspection_checklist = implode('#*#',$inspection_checklist);
	$not_complete = [];
	foreach($incomplete as $val) {
		$not_complete[] = implode('*#*',$val);
	}
	$not_complete = filter_var(implode('#*#',$not_complete),FILTER_SANITIZE_STRING);
	$comments = filter_var(htmlentities($_POST['comments']),FILTER_SANITIZE_STRING);
	$signed = $_POST['signature'];
	$immediate = ($_POST['attention_needed'] == 'Yes');
	$sql = "INSERT INTO `equipment_inspections` (`staffid`, `type`, `date`, `equipmentid`, `good_items`, `attention_needed`, `uncomplete`, `comments`, `immediate`, `signed`, `inspection_checklist`, `timer`)
		VALUES ('$staff', '$type', '$datetime', '$equipmentid', '$good_items', '$attention_needed', '$not_complete', '$comments', '$immediate', '$signed', '$inspection_checklist', '$timer')";
	mysqli_query($dbc, $sql);

	$id = mysqli_insert_id($dbc);
	$before_change = '';
	$history = "New equipment inspection Added. <br />";
	add_update_history($dbc, 'equipment_history', $history, '', $before_change);
	if (!empty($timer)) {
		$timer_values = explode(':', $timer);
		$total_hrs = floatval((($timer_values[0] * 3600) + ($timer_values[1] * 60) + $timer_values[2]) / 3600);
		$total_hrs = number_format($total_hrs, 2, '.', '');
		$today_date = date('Y-m-d', strtotime($_POST['date']));
		$comment_box = 'Hours from Inspection #'.$id;
		$sql = "INSERT INTO `time_cards` (`staff`, `date`, `type_of_time`, `total_hrs`, `comment_box`) VALUES ('$staff', '$today_date', 'Regular Hrs.', '$total_hrs', '$comment_box')";
		mysqli_query($dbc, $sql);
	}

	$img = sigJsonToImage($signed);
	if(!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	imagepng($img, 'download/sign_'.$id.'.png');

	//Prepare the variables
	include('../tcpdf/tcpdf.php');
	$filename = "download/inspection_report_".$id.".pdf";
	$logo = get_config($dbc, 'equipment_service_logo');
	$header = get_config($dbc, 'equipment_service_header');
	$footer = get_config($dbc, 'equipment_service_footer');

	//Generate the PDF
	DEFINE('HEADER_LOGO', $logo);
	DEFINE('HEADER_TEXT', html_entity_decode($header));
	DEFINE('FOOTER_TEXT', html_entity_decode($footer));

	class MYPDF extends TCPDF {

		//Page header
		public function Header() {
			if(HEADER_LOGO != '') {
				$image_file = 'download/'.HEADER_LOGO;
				$this->Image($image_file, 10, 5, 0, 25, '', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
			}

			if(HEADER_TEXT != '') {
				$this->setCellHeightRatio(0.7);
				$this->SetFont('helvetica', '', 8);
				$header_text = '<p style="text-align:right;">'.HEADER_TEXT.'</p>';
				$this->writeHTMLCell(0, 0, 0 , 5, $header_text, 0, 0, false, "R", true);
			}
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica', 'I', 8);
			$footer_text = '<p style="text-align:right;">'.$this->getAliasNumPage().'</p>';
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);

			if(FOOTER_TEXT != '') {
				$this->SetY(-30);
				$this->setCellHeightRatio(0.7);
				$this->SetFont('helvetica', '', 8);
				$footer_text = FOOTER_TEXT;
				$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "C", true);
			}
		}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetMargins(PDF_MARGIN_LEFT, (HEADER_LOGO != '' ? 35 : 20), PDF_MARGIN_RIGHT);

	$pdf->AddPage();
	$pdf->SetFont('helvetica', '', 8);
	$pdf->setCellHeightRatio(1);

	$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='$equipmentid'"));
	$html = '<h1>Service Request</h1>
	<p>Below are the details of the inspection:</p>
	<h2>Inspection</h2>
	<p>Submitted By: '.get_contact($dbc, $staff).'<br />
	Inspection Date: '.$_POST['date'].'<br />
	Total Hours: '.$_POST['timer'].'<br />
	Equipment Unit Number: '.$equipment['unit_number'].'<br />
	Comments: '.html_entity_decode($comments).'<br />
	Signature: <img src="download/sign_'.$id.'.png" width="190" height="80" border="0"></p>';

	$html .= '<h2>Inspection Checklist</h2>';
	$html .= '<table style="width: 80%;" align="center" style="border: 1px solid black; padding: 2px;">';
	$html .= '<tr>
			<th style="width: 30%; border: 1px solid black; padding: 2px;"><b>Name</b></th>
			<th style="width: 70%; border: 1px solid black; padding: 2px;"><b>Checklist</b></th>
		</tr>';
	$inspection_checklist = explode('#*#', $inspection_checklist);
	foreach ($inspection_checklist as $row) {
		$checklist_item = explode('**#**', $row);
		$html .= '<tr>';
		$html .= '<td align="left" style="width: 30%; border: 1px solid black; padding: 2px;">' . $checklist_item[0] . '</td>';
		$html .= '<td align="left" style="width: 70%; border: 1px solid black; padding: 2px;">';

		$checklist_options = '';
        $query = mysqli_query($dbc, "SELECT * FROM `field_config_equipment_inspection` WHERE `tab` = '".$equipment['category']."' AND `inspection_name` = '".$checklist_item[0]."'");
        if (mysqli_num_rows($query) > 0) {
			$result = mysqli_fetch_array($query);
			$checklist_options = $result['inspection_checklist'];
        }
        if (!empty($checklist_options)) {
        	$checklist_options = explode(',', $checklist_options);
        	foreach ($checklist_options as $checklist_option) {
        		if ($checklist_option == $checklist_item[1]) {
        			$img_src = 'radio_checked.png';
        		} else {
        			$img_src = 'radio_unchecked.png';
        		}
        		$html .= '<img src="../img/'.$img_src.'" style="height: 8px; width: 8px;"> '.$checklist_option.' ';
        	}
        } else {
        	if ($checklist_item[1] == 'Good') {
        		$img_src_good = 'radio_checked.png';
        		$img_src_atn = 'radio_unchecked.png';
        	} else {
        		$img_src_good = 'radio_unchecked.png';
        		$img_src_atn = 'radio_checked.png';
        	}
    		$html .= '<img src="../img/'.$img_src_good.'" style="height: 8px; width: 8px;"> Good ';
    		$html .= '<img src="../img/'.$img_src_atn.'" style="height: 8px; width: 8px;"> Needs Attention ';
        }
        if(!empty($checklist_item[2])) {
        	$html .= '<br />Details: '.$checklist_item[2];
        }
        $html .= '</td></tr>';
	}
	$html .= '</table>';

	// <h2>Attention Needed:</h2>
	// <ul>';
	// foreach($attention as $item) {
	// 	$html .= '<li>'.$item[0].(!empty($item[1]) ? ' - Details:<br />'.html_entity_decode($item[1]) : '').'</li>';
	// }
	// $html .= '</ul>
	// <h2>Good:</h2>
	// <ul>';
	// foreach($good as $item) {
	// 	$html .= '<li>'.$item[0].(!empty($item[1]) ? ' - Details:<br />'.html_entity_decode($item[1]) : '').'</li>';
	// }
	// $html .= '</ul>';
	if($not_complete != '') {
		$html .= '<p>The following details were not specified in the inspection:</p>
			<ul>';
		foreach($incomplete as $item) {
			$html .= '<li>'.$item[0].(!empty($item[1]) ? ' - Details:<br />'.html_entity_decode($item[1]) : '').'</li>';
		}
		$html .= '</ul>';
	}

	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output($filename, 'F');

	if($immediate == 'Yes') {
		//Prepare the Variables
		$staff_list = explode(',',get_config($dbc, 'equipment_service_alert'));
		$sender_name = get_config($dbc, 'equipment_service_sender_name');
		if($sender_name == '') {
			$sender_name = get_contact($dbc, $_SESSION['contactid']);
		}
		$sender = get_config($dbc, 'equipment_service_sender');
		if($sender == '') {
			$sender = get_email($dbc, $_SESSION['contactid']);
		}
		$subject = get_config($dbc, 'equipment_service_subject');
		$body = html_entity_decode(get_config($dbc, 'equipment_service_body')).'<br />Click <a href="'.WEBSITE_URL.'/Equipment/add_equipment.php?equipmentid='.$equipmentid.'">here</a> to view the equipment.';

		//Send the emails
		$email_list = [];
		foreach($staff_list as $staffid) {
				$email = get_email($dbc, $staffid);
				if($email != '') {
					$email_list[] = $email;
				}
		}
		try {
			send_email([$sender=>$sender_name], $email_list, '', '', $subject, $body, $filename);
		} catch(Exception $e) { echo "<script> alert('Unable to send service alert to ".get_contact($dbc,$staffid).". Please save the PDF and send it manually.'); window.location.replace('".$filename."'); </script>"; }
	}
	if(empty($_GET['edit'])) {
		echo "<script> window.location.replace('?tab=inspections'); </script>";
	} else {
		echo "<script> window.location.replace('?edit=".$equipmentid."&subtab=inspections'); </script>";
	}
} ?>
<script type="text/javascript">
$(document).ready(function () {
	$("[name=category]").change(function() {
		category = this.value;
		$.ajax({
			type: "GET",
			url: "inspection_ajax.php?category="+category+"&action=make",
			dataType: "html",
			success: function(response) {
				$("[name=make]").html(response);
				$("[name=make]").trigger("change.select2");
			}
		});
		$.ajax({
			type: "GET",
			url: "inspection_ajax.php?category="+category+"&action=model",
			dataType: "html",
			success: function(response) {
				$("[name=model]").html(response);
				$("[name=model]").trigger("change.select2");
			}
		});
		$.ajax({
			type: "GET",
			url: "inspection_ajax.php?category="+category+"&action=unit",
			dataType: "html",
			success: function(response) {
				$("[name=unit]").html(response);
				$("[name=unit]").trigger("change.select2");
			}
		});
		$.ajax({
			type: "GET",
			url: "inspection_ajax.php?category="+this.value+"&action=checklist",
			dataType: "html",
			success: function(response) {
				$("#tab_section_checklist .form-group").html(response);
			}
		});
	});
	$("[name=make]").change(function() {
		if(this.value != '') {
			$('[name=category]').val($(this).find('option:selected').data('category')).trigger('change.select2');
		}
		$('[name=model]').find('option').hide();
		$('[name=model]').find('[data-make="'+this.value+'"]').show();
		$('[name=model]').trigger('change.select2');
		$('[name=unit]').find('option').hide();
		$('[name=unit]').find('[data-make="'+this.value+'"]').show();
		$('[name=unit]').trigger('change.select2');
	});
	$("[name=model]").change(function() {
		if(this.value != '') {
			$('[name=category]').val($(this).find('option:selected').data('category')).trigger('change.select2');
			$('[name=make]').val($(this).find('option:selected').data('make')).trigger('change.select2');
		}
		$('[name=unit]').find('option').hide();
		$('[name=unit]').find('[data-model="'+this.value+'"]').show();
		$('[name=unit]').trigger('change.select2');
	});
	$("[name=unit]").change(function() {
		if(this.value != '') {
			$('[name=category]').val($(this).find('option:selected').data('category')).trigger('change.select2');
			$('[name=make]').val($(this).find('option:selected').data('make')).trigger('change.select2');
			$('[name=model]').val($(this).find('option:selected').data('model')).trigger('change.select2');
		}
	});
	if($("[name=category]").val() != '') {
		$.ajax({
			type: "GET",
			url: "inspection_ajax.php?category="+$("[name=category]").val()+"&action=checklist",
			dataType: "html",
			success: function(response) {
				$("#tab_section_checklist .form-group").html(response);
			}
		});
	}

	var hasTimer = false;
	// Init timer start
	$('.start-timer-btn').on('click', function() {
		hasTimer = true;
		$('.timer').timer({
			editable: true
		});
		$(this).addClass('hidden');
		$('.pause-timer-btn, .remove-timer-btn').removeClass('hidden');
		return false;
	});

	// Init timer pause
	$('.pause-timer-btn').on('click', function() {
		$('.timer').timer('pause');
		$(this).addClass('hidden');
		$('.resume-timer-btn').removeClass('hidden');
		return false;
	});

	// Init timer resume
	$('.resume-timer-btn').on('click', function() {
		$('.timer').timer('resume');
		$(this).addClass('hidden');
		$('.pause-timer-btn, .remove-timer-btn').removeClass('hidden');
		return false;
	});

	// Active tabs
	$('[data-tab-target]').click(function() {
		$('.main-screen .main-screen').scrollTop($('#tab_section_'+$(this).data('tab-target')).offset().top + $('.main-screen .main-screen').scrollTop() - $('.main-screen .main-screen').offset().top);
		return false;
	});
	setTimeout(function() {
		$('.main-screen .main-screen').scroll(function() {
			var screenTop = $('.main-screen .main-screen').offset().top + 10;
			var screenHeight = $('.main-screen .main-screen').innerHeight();
			$('.active.blue').removeClass('active blue');
			$('.tab-section').filter(function() { return $(this).offset().top + this.clientHeight > screenTop && $(this).offset().top < screenTop + screenHeight; }).each(function() {
				$('[data-tab-target='+$(this).attr('id').replace('tab_section_','')+']').find('li').addClass('active blue');
			});
		});
		$('.main-screen .main-screen').scroll();
	}, 500);
});
</script>

<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
	<ul>
		<a href="?<?= empty($_GET['edit']) ? 'tab=inspections' : 'edit='.$_GET['edit'] ?>"><li>Back to Dashboard</li></a>
		<a href="" data-tab-target="info"><li class="active blue">Inspection Information</li></a>
		<a href="" data-tab-target="equipment"><li>Equipment Details</li></a>
		<a href="" data-tab-target="checklist"><li>Equipment Checklist</li></a>
		<a href="" data-tab-target="comment"><li>Comments</li></a>
		<a href="" data-tab-target="sign"><li>Sign Off</li></a>
	</ul>
</div>

<div class="scale-to-fill has-main-screen" style="overflow: hidden;">
	<div class="main-screen standard-body form-horizontal">
		<div class="standard-body-title">
			<h3>Inspection</h3>
		</div>

		<div class="standard-body-content">
			<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

				<?php
				$staff = $_SESSION['contactid'];
				$equipmentid = '';
				$category = '';
				$make = '';
				$model = '';

				if(!empty($_GET['edit'])) {
					$equipmentid = $_GET['edit'];
					$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `make`, `model` FROM `equipment` WHERE `equipmentid`='$equipmentid'"));
					$category = $equipment['category'];
					$make = $equipment['make'];
					$model = $equipment['model'];
				}
				else if(!empty($_GET['category'])) {
					$category = $_GET['category'];
				} ?>

				<div id="tab_section_info" class="tab-section col-sm-12">
					<h4>Inspection Information</h4>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Staff:</label>
						<div class="col-sm-8">
							<select name="staffid" data-placeholder="Select Staff" class="chosen-select-deselect form-control"><option></option>
								<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
								foreach($staff_list as $id) {
									echo "<option ".($id == $staff ? 'selected' : '')." value='$id'>".get_contact($dbc, $id)."</option>";
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Inspection Tab:</label>
						<div class="col-sm-8">
							<select name="type" data-placeholder="Select Tab" class="chosen-select-deselect form-control"><option></option>
								<option value="Pre Trip">Pre Trip</option>
								<option value="Post Trip">Post Trip</option>
								<option value="Maintenance">Maintenance</option>
								<option value="Evaluation">Evaluation</option>
								<option value="Weekly Equipment">Weekly Equipment</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Inspection Date:</label>
						<div class="col-sm-8">
							<input type="text" name="date" value="<?= date('Y-m-d') ?>" class="form-control datepicker">
						</div>
					</div>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Inspection Time:</label>
						<div class="col-sm-8">
							<input type="text" name="time" value="<?= date('g:i A') ?>" class="form-control datetimepicker">
						</div>
					</div>
					<div class="form-group">
						<label for="fax_number" class="col-sm-4 control-label">Timer:</label>
						<div class="col-sm-8">
				            <input type='text' name='timer' id='timer_value' style="width: 50%; float: left;" class='form-control timer' placeholder='0 sec' />&nbsp;&nbsp;
				            <button class='btn btn-success start-timer-btn brand-btn mobile-block'>Start</button>
				            <button class='btn btn-success resume-timer-btn hidden brand-btn mobile-block'>Resume/End Break</button>
				            <button class='btn pause-timer-btn hidden brand-btn mobile-block'>Pause/Break</button>
	    				</div>
					</div>
					<hr>
				</div>

				<div id="tab_section_equipment" class="tab-section col-sm-12">
					<h4>Equipment Details</h4>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Tab:</label>
						<div class="col-sm-8">
							<select name="category" data-placeholder="Select a Tab" class="chosen-select-deselect form-control"><option></option>
								<?php $list = mysqli_query($dbc, "SELECT `category` FROM `equipment` WHERE `deleted`=0 $access_query GROUP BY `category`");
								while($row = mysqli_fetch_array($list)) {
									echo "<option ".($category == $row['category'] ? 'selected' : '')." value='".$row['category']."'>".$row['category']."</option>";
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Make:</label>
						<div class="col-sm-8">
							<select name="make" data-placeholder="Select a Make" class="chosen-select-deselect form-control"><option></option>
								<?php $list = mysqli_query($dbc, "SELECT `category`, `make` FROM `equipment` WHERE `deleted`=0 $access_query GROUP BY `make`");
								while($row = mysqli_fetch_array($list)) {
									echo "<option ".($make == $row['make'] ? 'selected' : ($category != $row['category'] ? 'style="display:none;"' : ''))." value='".$row['make']."' data-category='".$row['category']."'>".$row['make']."</option>";
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Model:</label>
						<div class="col-sm-8">
							<select name="model" data-placeholder="Select a Model" class="chosen-select-deselect form-control"><option></option>
								<?php $list = mysqli_query($dbc, "SELECT `category`, `make`, `model` FROM `equipment` WHERE `deleted`=0 $access_query GROUP BY `model`");
								while($row = mysqli_fetch_array($list)) {
									echo "<option ".($model == $row['model'] ? 'selected' : ($category != $row['category'] ? 'style="display:none;"' : ''))." value='".$row['model']."' data-category='".$row['category']."' data-make='".$row['make']."'>".$row['model']."</option>";
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Unit Number:</label>
						<div class="col-sm-8">
							<select name="unit" data-placeholder="Select a Unit Number" class="chosen-select-deselect form-control"><option></option>
								<?php $list = mysqli_query($dbc, "SELECT `category`, `make`, `model`, `unit_number`, `equipmentid` FROM `equipment` WHERE `deleted`=0 $access_query");
								while($row = mysqli_fetch_array($list)) {
									echo "<option ".($equipmentid == $row['equipmentid'] ? 'selected' : ($category != $row['category'] ? 'style="display:none;"' : ''))." value='".$row['equipmentid']."' data-category='".$row['category']."' data-make='".$row['make']."' data-model='".$row['model']."'>".$row['unit_number']."</option>";
								} ?>
							</select>
						</div>
					</div>
					<hr>
				</div>

				<div id="tab_section_checklist" class="tab-section col-sm-12">
					<h4>Equipment Checklist</h4>
					<div class="form-group">
						<h4>Please select a tab to view the relevant checklist.</h4>
					</div>
					<hr>
				</div>

				<div id="tab_section_comment" class="tab-section col-sm-12">
					<h4>Comments</h4>
					<div class="form-group">
						<label class="col-sm-4 control-label">Comments:</label>
						<div class="col-sm-8">
							<textarea name="comments" class="form-control"></textarea>
						</div>
					</div>
					<hr>
				</div>

				<div id="tab_section_sign" class="tab-section col-sm-12">
					<h4>Sign Off</h4>
					<div class="form-group">
						<label class="col-sm-4 control-label">Is immediate attention of this equipment required?</label>
						<div class="col-sm-8">
							<label class="form-checkbox small"><input type="radio" name="attention_needed" value="Yes"> Yes</label>
							<label class="form-checkbox small"><input type="radio" name="attention_needed" value="No"> No</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Signature:</label>
						<div class="col-sm-8">
							<?php $output_name = 'signature';
							include('../phpsign/sign_multiple.php'); ?>
						</div>
					</div>
					<hr>
				</div>

				<div class="form-group">
					<div class="col-sm-6">
						<p><span class="brand-color"><em>Required Fields *</em></span></p>
					</div>
					<div class="col-sm-6">
						<div class="pull-right">
							<a href="?<?= empty($_GET['edit']) ? 'tab=inspections' : 'edit='.$_GET['edit'] ?>" class="btn brand-btn">Back</a>
							<button	type="submit" name="submit"	value="Submit" class="btn brand-btn">Submit</button>
						</div>
					</div>
				</div>

			</form>
		</div>
	</div>
</div>
