<?php include_once('../include.php');
checkAuthorised('calendar_rook');
include_once('calendar_settings_inc.php');

if(isset($_POST['export_pdf'])) {
	include('../tcpdf/tcpdf.php');
	$scheduling_time_table_logo = get_config($dbc, 'scheduling_time_table_logo');
	$scheduling_time_table_logo_align = !empty(get_config($dbc, 'scheduling_time_table_logo_align')) ? get_config($dbc, 'scheduling_time_table_logo_align') : 'L';
	$region = $_POST['region'];
	$export_by = $_POST['export_by'];
	$equipmentid = $_POST['equipmentid'];
	$staffid = $_POST['staffid'];
	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];
	$group_by_class = $_POST['group_by_class'];

	$region_query = '';
	if(!empty($region)) {
		$region_query = " AND `tickets`.`region` = '".$region."'";
	}

	$classifications = [""];
	if($group_by_class == 1) {
		$classifications = [];
		foreach($contact_classifications as $i => $classification) {
			if(in_array($region, $classification_regions[$i]) || empty($region)) {
				$classifications[] = $classification;
			}
		}
	}

	$blocks = [];
	foreach($classifications as $classification) {
		$class_query = '';
		if(!empty($classification)) {
			$class_query = " AND `tickets`.`classification` = '".$classification."'";
		}
		for($cur_date = $start_date; strtotime($cur_date) <= strtotime($end_date); $cur_date = date('Y-m-d', strtotime($cur_date.' + 1 day'))) {
			$sql = "SELECT IFNULL(`ticket_schedule`.`status`,`tickets`.`status`) `status`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, `tickets`.`contactid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$cur_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$cur_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND IFNULL(IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`),'') != '' AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$region_query.$class_query;
			$query = mysqli_query($dbc, $sql);
			while($row = mysqli_fetch_assoc($query)) {
				$team_contactids = [];
				$contactids = [];
				$equip_assign = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipmentid` = '".$row['equipmentid']."' AND  '$cur_date' BETWEEN `start_date` AND `end_date` AND `deleted` = 0 AND CONCAT(',',`hide_days`,',') NOT LIKE '%,".$cur_date.",%'"));
				$equip_assign_staffs = mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `deleted` = 0 AND `equipment_assignmentid` = '".$equip_assign['equipment_assignmentid']."'");
				while($ea_staff = mysqli_fetch_assoc($equip_assign_staffs)) {
					if(strpos(','.$equip_assign['hide_staff'].',', ','.$ea_staff['contactid'].',') === FALSE) {
						$contactids[] = $ea_staff['contactid'];
			    		$team_contactids[$ea_staff['contactid']] = [get_contact($dbc, $ea_staff['contactid'], 'category'), get_contact($dbc, $ea_staff['contactid']), $ea_staff['contact_position']];
					}
				}

				$equip_assign_team = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '".$row['teamid']."'"));

			    $team_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` ='".$equip_assign_team['teamid']."' AND `deleted` = 0"),MYSQLI_ASSOC);
			    foreach ($team_contacts as $team_contact) {
					if(strpos(','.$equip_assign['hide_staff'].',', ','.$team_contact['contactid'].',') === FALSE) {
						$contactids[] = $ea_staff['contactid'];
			    		$team_contactids[$team_contact['contactid']] = [get_contact($dbc, $team_contact['contactid'], 'category'), get_contact($dbc, $team_contact['contactid']), $team_contact['contact_position']];
			    	}
			    }

				if($export_by == 'Staff') {
					$contactids = array_filter(array_unique(array_merge(explode(',', $row['contactid']), $contactids)));
					foreach($contactids as $contactid) {
						if(in_array($contactid, $staffid)) {
							if(empty($blocks[$classification][$cur_date][$contactid]['label'])) {
								$blocks[$classification][$cur_date][$contactid]['label'] = '<b>'.get_contact($dbc, $contactid).'</b>';
							}
							$blocks[$classification][$cur_date][$contactid]['total']++;
							if(in_array($row['status'], $calendar_checkmark_status)) {
								$blocks[$classification][$cur_date][$contactid]['completed']++;
							}
						}
					}
				} else if(in_array($row['equipmentid'],$equipmentid)) {
					if(empty($blocks[$classification][$cur_date][$row['equipmentid']]['label'])) {
						$equipment_label = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(`category`, ' #', `unit_number`) label FROM `equipment` WHERE `equipmentid` = '".$row['equipmentid']."'"))['label'];
						$team_name = [];;
					    foreach ($team_contactids as $key => $value) {
					    	$cur_staff = $value[0].': '.(!empty($value[2]) ? $value[2].': ' : '').$value[1];
					    	$team_name[] = $cur_staff;
					    }
					    $team_name = implode('<br />',$team_name);
					    $team_name = '<b>'.$equipment_label.'</b>'.(!empty($team_name) ? '<br />'.$team_name : '');
						$blocks[$classification][$cur_date][$row['equipmentid']]['label'] = $team_name;
					}
					$blocks[$classification][$cur_date][$row['equipmentid']]['total']++;
					if(in_array($row['status'], $calendar_checkmark_status)) {
						$blocks[$classification][$cur_date][$row['equipmentid']]['completed']++;
					}
				}
			}
		}
	}

	DEFINE(FORM_HEADER_TEXT, 'Time Table ('.date('M j, Y', strtotime($start_date)).' - '.date('M j, Y', strtotime($end_date)).')');
	DEFINE(FORM_HEADER_LOGO, $scheduling_time_table_logo);
	$logo_height = 0;
	$logo_width = 0;
	if(file_exists('download/'.FORM_HEADER_LOGO)) {
		list($image_width, $image_height) = getimagesize('download/'.FORM_HEADER_LOGO);
		$logo_height = $image_height;
		$logo_width = $image_width;
		if($image_height > 180) {
			$logo_width = (180 / $logo_height) * 100;
			$logo_height = 180;
		}
		if($logo_width > 360) {
			$logo_height = (360 / $logo_width) * 100;
			$logo_width = 360;
		}
		$logo_height = $logo_height / 7.2;
		$logo_width = $logo_width / 7.2;
	}
	DEFINE(FORM_HEADER_LOGO_HEIGHT, $logo_height);
	DEFINE(FORM_HEADER_LOGO_WIDTH, $logo_width);

    class MYPDF extends TCPDF {

        //Page header
        public function Header() {
            if(FORM_HEADER_LOGO != '') {
                $image_file = '../Calendar/download/'.FORM_HEADER_LOGO;
                $this->Image($image_file, 10, 5, FORM_HEADER_LOGO_WIDTH, FORM_HEADER_LOGO_HEIGHT, '', '', 'T', false, 300, FORM_HEADER_LOGO_ALIGN, false, false, 0, false, false, false);
            }

            if(FORM_HEADER_TEXT != '') {
	            $this->SetFont('helvetica', '', 14);
                $this->setCellHeightRatio(0.7);
                $this->writeHTMLCell(0, 0, 7.5, 10, FORM_HEADER_TEXT, 0, 0, false, true, 'C', true);
            }
        }

        //Page footer
        public function Footer() {
            $this->SetY(-10);
            $this->SetFont('helvetica', '', 6);
            $this->writeHTMLCell(0, 0, '', '', 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), 0, 0, false, true, 'R', true);
        }
    }

    $pdf = new MYPDF('L', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
    $pdf->SetMargins(PDF_MARGIN_LEFT, (FORM_HEADER_LOGO != '' ? 35 : 20), PDF_MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(TRUE, 25);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);
    $pdf->setCellHeightRatio(1);

    $table_start_date = date('Y-m-d', strtotime('last Sunday', strtotime($start_date)));
    $table_end_date = date('Y-m-d', strtotime('next Saturday', strtotime($end_date)));
	if(date('l', strtotime($start_date)) == 'Sunday') {
		$table_start_date = $start_date;
	}
	if(date('l', strtotime($end_date)) == 'Saturday') {
		$table_end_date = $end_date;
	}

	$html = '';

	foreach($classifications as $classification) {
		if(!empty($classification)) {
			$html .= '<h3>'.$classification.'</h3>';
		}
		for($cur_date = $table_start_date; strtotime($cur_date) < strtotime($table_end_date); $cur_date = date('Y-m-d', strtotime($cur_date.' + 7 days'))) {
			$html .= '<table border="1" cellpadding="2">';
			$html .= '<tr>';
			for($table_date = $cur_date; strtotime($table_date) < strtotime(date('Y-m-d', strtotime($cur_date.' + 7 days'))); $table_date = date('Y-m-d', strtotime($table_date.' + 1 day'))) {
				$html .= '<td style="background-color: #ccc;"><b>'.date('D M j, Y', strtotime($table_date)).'</b></td>';
			}
			$html .= '</tr>';
			$html .= '</table>';

			$html .= '<table border="1">';
			$html .= '<tr>';
			for($table_date = $cur_date; strtotime($table_date) < strtotime(date('Y-m-d', strtotime($cur_date.' + 7 days'))); $table_date = date('Y-m-d', strtotime($table_date.' + 1 day'))) {
				$html .= '<td>';
				$html .= '<table border="0" cellpadding="2">';
				$block_htmls = [];
				foreach($blocks[$classification][$table_date] as $block) {
					$block_html = $block['label'].'<br />';
					$block_html .= '(Completed '.(empty($block['completed']) ? 0 : $block['completed']).' of '.(empty($block['total']) ? 0 : $block['total']).' '.TICKET_TILE.')';
					$block_htmls[] = $block_html;
				}
				if(!empty($block_htmls)) {
					$html .= '<tr><td style="border-bottom: 1px solid black;">'.implode('</td></tr><tr><td style="border-bottom: 1px solid black;">', $block_htmls).'</td></tr>';
				} else {
					$html .= '<tr><td></td></tr>';
				}
				$html .= '</table></td>';
			}
			$html .= '</tr>';
			$html .= '</table>';
		}
	}
    $pdf->writeHTML(utf8_encode($html), true, false, true, false, '');

    if(!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    $today_date = date('Y-m-d_H-i-a', time());
    $file_name = 'time_table_'.$today_date.'.pdf';
    $pdf->Output('download/'.$file_name, 'F');

    echo '<script type="text/javascript">
            window.top.open("download/'.$file_name.'", "_blank");
        </script>';

}
$equipment_category = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['equipment_category'];
$equipment_categories = array_filter(explode(',', $equipment_category));
if(empty($equipment_categories) || count($equipment_categories) > 1) {
    $equipment_category = 'Equipment';
}
$equip_cat_query = '';
if(count($equipment_categories) > 0) {
    $equip_cat_query = " AND `equipment`.`category` IN ('".implode("','", $equipment_categories)."')";
}
$start_date = date('Y-m-d', strtotime('last Sunday', strtotime(date('Y-m-d'))));
$end_date = date('Y-m-d', strtotime('next Saturday', strtotime(date('Y-m-d'))));
if(date('l') == 'Sunday') {
	$start_date = date('Y-m-d');
} else if(date('l') == 'Saturday') {
	$end_date = date('Y-m-d');
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$('[name="export_by"]').change(function() { filterExportBy(); });
});
$(document).on('change', 'select[name="region"]', function() { filterRegion(); });
$(document).on('change', 'select[name="equipmentid[]"]', function() { filterEquipment(); });
$(document).on('change', 'select[name="staffid[]"]', function() { filterStaff(); });
function filterExportBy() {
	if($('[name="export_by"]:checked').val() == 'Staff') {
		$('.equip_div').hide();
		$('.staff_div').show();
	} else {
		$('.equip_div').show();
		$('.staff_div').hide();
	}
}
function filterRegion() {
	var region = $('[name="region"]').val();
	if(region != undefined && region != '') {
		$('[name="equipmentid[]"] option:not([value="ALL"]').hide();
		$('[name="equipmentid[]"] option[data-region="'+region+'"]').show();
		$('[name="equipmentid[]"] option').prop('selected', false);
		$('[name="staffid[]"] option:not([value="ALL"]').hide();
		$('[name="staffid[]"] option[data-region="'+region+'"]').show();
		$('[name="staffid[]"] option').prop('selected', false);
	} else {
		$('[name="equipmentid[]"] option').show();
		$('[name="staffid[]"] option').show();
	}
	$('[name="equipmentid[]"]').trigger('change.select2');
	$('[name="staffid[]"]').trigger('change.select2');
}
function filterEquipment() {
	if($('[name="equipmentid[]"] option[value="ALL"]').is(':selected')) {
		$('[name="equipmentid[]"] option:visible').prop('selected', true);
	}
	$('[name="equipmentid[]"] option[value="ALL"]').prop('selected', false);
	$('[name="equipmentid[]"]').trigger('change.select2');
}
function filterStaff() {
	if($('[name="staffid[]"] option[value="ALL"]').is(':selected')) {
		$('[name="staffid[]"] option:visible').prop('selected', true);
	}
	$('[name="staffid[]"] option[value="ALL"]').prop('selected', false);
	$('[name="staffid[]"]').trigger('change.select2');
}
</script>

<div class="container">
    <div class="row">
	    <h3 class="inline">Export Time Table</h3>
	    <div class="pull-right gap-top"><a href=""><img src="../img/icons/cancel.png" alt="Close" title="Close" class="inline-img" /></a></div>
	    <div class="clearfix"></div>
	    <hr />
		<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
			<div class="form-group">
				<label class="col-sm-4 control-label">Region:</label>
				<div class="col-sm-8">
					<select name="region" class="chosen-select-deselect">
						<option></option>
						<?php $region_list = $contact_regions;
						foreach($region_list as $region) {
							echo '<option value="'.$region.'">'.$region.'</option>';
						} ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Export By:</label>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="radio" name="export_by" value="" checked> <?= $equipment_category ?></label>
					<label class="form-checkbox"><input type="radio" name="export_by" value="Staff"> Staff</label>
				</div>
			</div>
			<div class="form-group equip_div">
				<label class="col-sm-4 control-label"><?= $equipment_category ?>:</label>
				<div class="col-sm-8">
					<select name="equipmentid[]" multiple class="chosen-select-deselect">
						<option value="ALL">Select All</option>
						<?php $equip_options = explode(',',get_config($dbc,'equip_options'));
						$equip_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT *, CONCAT(`category`, ' #', `unit_number`) label FROM `equipment` WHERE `deleted`=0 ".$equip_cat_query." $allowed_equipment_query $customer_query ORDER BY ".(in_array('region_sort',$equip_options) ? "IFNULL(NULLIF(`region`,''),'ZZZ'), " : '')."`label`"),MYSQLI_ASSOC);
						foreach($equip_list as $equipment) {
							echo '<option value="'.$equipment['equipmentid'].'" data-region="'.$equipment['region'].'">'.$equipment['label'].'</option>';
						} ?>
					</select>
				</div>
			</div>
			<div class="form-group staff_div" style="display: none;">
				<label class="col-sm-4 control-label">Staff:</label>
				<div class="col-sm-8">
					<select name="staffid[]" multiple class="chosen-select-deselect">
						<option value="ALL">Select All</option>
						<?php $get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"));
						$contact_category = !empty($get_field_config) ? explode(',', $get_field_config['contact_category']) : '';
						$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `region` FROM `contacts` WHERE `category` IN (".("'".implode("','",$contact_category)."'").") AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query.$allowed_roles_query));
						foreach($staff_list as $staff) {
							echo '<option value="'.$staff['contactid'].'" data-region="'.$staff['region'].'">'.$staff['full_name'].'</option>';
						} ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Date:</label>
				<div class="col-sm-8">
					<div class="pull-left"><input type="text" name="start_date" class="form-control datepicker" value="<?= $start_date ?>"></div>
					<div class="pull-left pad-left pad-right"> - </div>
					<div class="pull-left"><input type="text" name="end_date" class="form-control datepicker" value="<?= $end_date ?>"></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">PDF Options:</label>
				<div class="col-sm-8">
					<label class="form-checkbox">
						<input type="checkbox" name="group_by_class" value="1"> Group By Classification</label>
					</label>
				</div>
			</div>

		    <div class="pull-right" style="padding-top: 1em;">
		        <a href="?" class="btn brand-btn mobile-anchor">Cancel</a>
		    	<button type="submit" name="export_pdf" value="Submit" class="btn brand-btn">Submit</button>
		    </div>
		</form>
	</div>
</div>