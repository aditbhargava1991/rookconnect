<?php
include('../include.php');
include_once('../Timesheet/reporting_functions.php');
?>
<style>table td { background-color:transparent; }</style>
</head>
<body>
<?php
include_once ('../navigation.php');
checkAuthorised('timesheet');
include 'config.php';

if($_GET['export'] == 'pdf') {
	include_once('../tcpdf/tcpdf.php');
	$file_name = 'timesheets_'.date('Y_m_d_h_t').'.pdf';
	$search_staff = filter_var($_GET['search_staff'],FILTER_SANITIZE_STRING);
	$search_start_date = filter_var($_GET['search_start_date'],FILTER_SANITIZE_STRING);
	$search_end_date = filter_var($_GET['search_end_date'],FILTER_SANITIZE_STRING);
	$search_position = filter_var($_GET['search_position'],FILTER_SANITIZE_STRING);
	$search_project = filter_var($_GET['search_project'],FILTER_SANITIZE_STRING);
	$search_ticket = filter_var($_GET['search_ticket'],FILTER_SANITIZE_STRING);
	$override_value_config = '';
	if(!empty($_GET['value_config'])) {
		$override_value_config = $_GET['value_config'];
	}
	$report = get_hours_report($dbc, $search_staff, $search_start_date, $search_end_date, $search_position, $search_project, $search_ticket, 'to_array', $config['hours_types'], $override_value_config);

	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetFont('dejavusans', '', 8);
	foreach($report as $report_page) {
		$pdf->AddPage();
		$pdf->writeHTML($report_page, true, false, true, false, '');
	}
	$pdf->Output($file_name, 'F');
	echo "<script> window.location.replace('$file_name'); </script>";
}
if($_GET['export'] == 'pdf_egs') {
	include_once('../tcpdf/tcpdf.php');

	$file_name = 'payroll_summary_'.date('Y_m_d_h_t').'.pdf';
	$search_staff = explode(',',$_GET['search_staff']);
	$search_start_date = filter_var($_GET['search_start_date'],FILTER_SANITIZE_STRING);
	$search_end_date = filter_var($_GET['search_end_date'],FILTER_SANITIZE_STRING);
    $search_position = filter_var($_GET['search_position'],FILTER_SANITIZE_STRING);
    $search_project = filter_var($_GET['search_project'],FILTER_SANITIZE_STRING);
    $search_ticket = filter_var($_GET['search_ticket'],FILTER_SANITIZE_STRING);
	$override_value_config = '';
	if(!empty($_GET['value_config'])) {
		$override_value_config = $_GET['value_config'];
	}
	$override_timesheet_payroll_fields = '';
	if(!empty($_GET['payroll_value_config'])) {
		$override_timesheet_payroll_fields = $_GET['payroll_value_config'];
	}
    //$logo = get_config($dbc, 'logo_upload');
    $logo = get_config($dbc, 'timesheet_pdf_logo');

    //$img = !empty($logo) ? file_get_contents(WEBSITE_URL.'Se/download/'.$logo) : get_config($dbc, 'company_name'); echo $img;exit;
    $report = '';

	$report .= '<table cellspacing="10">
				<tr>
                    <td colspan="2" align="center">'.( !empty($logo) ? '<img height="100" width="120" src="download/'.$logo.'" />' : '<h1>'.get_config($dbc, 'company_name').'</h1>' ).'</td>
                </tr>
                <tr>
                    <td colspan="2"><h2>Payroll Summary - Daily Hours</h2></td>
                </tr>
                <tr>
                    <td>Date Range:    '.$search_start_date.' to '.$search_end_date.'</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Approved for Payroll: Show All</td>
                    <td align="right"><i>Note: hours have lunch deductions pre-applied where applicable</i></td>
                </tr>
                ';

    if(!empty($_GET['see_staff']) || $_GET['timesheet_tab'] != 'payroll') {
		$report .= get_egs_hours_report($dbc, $search_staff, $search_start_date, $search_end_date,$search_staff,$report_format = 'to_array',$_GET['timesheet_tab'], $override_value_config, $override_timesheet_payroll_fields);
    } else {
		$report .= get_egs_main_hours_report($dbc, $search_staff, $search_start_date, $search_end_date,$report_format = 'to_array',$_GET['timesheet_tab']);
	}

	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->AddPage();
	$pdf->SetFont('helvetica', '', 8);
    $pdf->setCellHeightRatio(1.2);
	$pdf->writeHTML($report, true, false, true, false, '');
	$pdf->Output($file_name, 'F');
	echo "<script> window.location.replace('$file_name'); </script>";
}

else if(isset($_GET['action']) && $_GET['action'] == 'pdf') {
  ob_clean();
  include_once('../tcpdf/tcpdf.php');

  $options_array = array(
      'Regular Hrs.',
      'Extra Hrs.',
      'Relief Hrs.',
      'Sleep Hrs.',
      'Sick Time Adj.',
      'Sick Hrs.Taken',
      'Stat Hrs.',
      'Stat Hrs.Taken',
      'Vac Hrs.',
      'Vac Hrs.Taken'
  );

  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $pdf->SetFont('dejavusans', '', 8);
  $pdf->AddPage();

  $html = '';
  $timesheet_layout = get_config($dbc, 'timesheet_layout');
  if($timesheet_layout == 'position_dropdown' || $timesheet_layout == 'ticket_task') {
	  $html .= '<br><br><table width="100%">
	  <tr>
		<td>CLIENT NAME</td>
		<td></td>
		<td>Name :</td>
		<td></td>
		<td>Employee No.</td>
		<td></td>
	  </tr>
	  <tr>
		<td></td>
		<td></td>
		<td>Location:</td>
		<td></td>
		<td></td>
		<td></td>
	  </tr>
	  <tr>
		<td></td>
		<td></td>
		<td>Position:</td>
		<td></td>
		<td>Hrs/wk:</td>
		<td></td>
	  </tr>
	  <tr>
		<td></td>
		<td></td>
		<td>Commencement Date:</td>
		<td></td>
		<td></td>
		<td></td>
	  </tr>
	  </table><br><br>
	  <p align="center">EMPLOYEE TIMESHEET</p>
	  <table>
		<tr>
		  <td>Pay Period #</td>
		</tr>
		<tr>
		  <td>NET HOURS CARRIED:</td>
		</tr>
		<tr>
		  <td>BALANCE FORWARD Y.T.D.:</td>
		</tr>
	  </table>
	  <br><br>';
	  $html .= '<table>
		<tr>
			<th style="text-align:center; vertical-align:bottom; width:7em;"><div>Date</div></th>
			'.(in_array('schedule',$value_config) ? '<th style="text-align:center; vertical-align:bottom; width:9em;"><div>Schedule</div></th>' : '').'
			'.(in_array('scheduled',$value_config) ? '<th style="text-align:center; vertical-align:bottom; width:10em;"><div>Scheduled Hours</div></th>' : '');
		if($timesheet_layout == 'ticket_task') {
			$html .= '<th style="text-align:center; vertical-align:bottom; width:12em;"><div>'.TICKET_NOUN.'</div></th>';
			$html .= '<th style="text-align:center; vertical-align:bottom; width:12em;"><div>Task</div></th>';
		} else {
			$html .= '<th style="text-align:center; vertical-align:bottom; width:12em;"><div>Position</div></th>';
		}
		$html .= '<th style="text-align:center; vertical-align:bottom; width:12em;"><div>Position</div></th>
			'.(in_array('total_tracked_hrs',$value_config) ? '<th style="text-align:center; vertical-align:bottom; width:6em;"><div>Time Tracked</div></th>' : '').'
			<th style="text-align:center; vertical-align:bottom; width:6em;"><div>Hours</div></th>
			<th style="text-align:center; vertical-align:bottom;"><div>Comments</div></th>
		</tr>';
		$total = 0;
		$limits = "AND `staff`='$search_staff' AND `approv`='N'";
		if($search_site > 0) {
			$limits .= " AND IFNULL(`business`,'') LIKE '%$search_site%'";
		}
		$result = get_time_sheet($search_start_date, $search_end_date, $limits, ', `staff`, `date`, `time_cards_id`');
		$date = $search_start_date;
		$i = 0;
		while(strtotime($date) <= strtotime($search_end_date)) {
			$timecardid = 0;
			$row = '';
			$comments = '';
			if($result[$i]['date'] == $date) {
				$row = $result[$i++];
				$comments = '';
				if(in_array('project',$value_config)) {
					foreach(explode(',',$row['PROJECTS']) as $projectid) {
						if($projectid > 0) {
							$comments .= get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"))).'<br />';
						}
					}
				}
				if(in_array('search_client',$value_config)) {
					foreach(explode(',',$row['CLIENTS']) as $clientid) {
						if($clientid > 0) {
							$comments .= get_contact($dbc, $clientid).'<br />';
						}
					}
				}
				$comments .= html_entity_decode($row['COMMENTS']);
				$total += $row['hours'];
				$timecardid = $row['time_cards_id'];
			}
			$day_of_week = date('l', strtotime($date));
			$shifts = checkShiftIntervals($dbc, $search_staff, $day_of_week, $date, 'all');
			if(!empty($shifts)) {
				$hours = '';
				$hours_off = '';
				foreach ($shifts as $shift) {
					$hours .= $shift['starttime'].' - '.$shift['endtime'].'<br>';
					$hours_off = $shift['dayoff_type'] == '' ? $hours_off : $shift['dayoff_type'];

				}
				$hours = $hours_off == '' ? $hours : $hours_off;
			} else {
				$hours = $schedule_list[date('w',strtotime($date))];
			}
			$mod = '';
			if($date < $last_period) {
				$mod = 'readonly';
			}
			$html .= '<tr>
				<td data-title="Date">'.$date.'</td>
				'.(in_array('schedule',$value_config) ? '<td data-title="Schedule"><?= $hours ?></td>' : '').'
				'.(in_array('scheduled',$value_config) ? '<td data-title="Scheduled Hours"><?= $hours ?></td>' : '');
			if($timesheet_layout == 'ticket_task') {
				$html .= '<td data-title="'.TICKET_NOUN.'">'.get_ticket_label($dbc, mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$row['ticketid']."'"))).'</td>';
				$html .= '<td data-title="Task">'.$row['type_of_time'].'</td>';
			} else {
				$html .= '<td data-title="Position">'.$row['type_of_time'].'</td>';
			}
			$html .= (in_array('total_tracked_hrs',$value_config) ? '<td data-title="Time Tracked">'.$row['timer'].'</td>' : '').'
				<td data-title="Hours">'.time_decimal2time($row['hours']).'</td>
				<td data-title="Comments">'.$comments.'</td>
			</tr>';
			if($date != $row['date']) {
				$date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
			}
		}
		$html .= '<tr>
			<td data-title="" colspan="'.($timesheet_layout == 'ticket_task' ? '3' : '2').'">Totals</td>
			'.(in_array('schedule',$value_config) ? '<td></td>' : '').'
			'.(in_array('scheduled',$value_config) ? '<td></td>' : '').'
			'.(in_array('total_tracked_hrs',$value_config) ? '<td></td>' : '').'
			<td data-title="Total Hours">'.time_decimal2time($total).'</td>
			<td></td>
		</tr>
	</table>';
  } else {
	  $html .= '<br><br><table width="100%">
	  <tr>
		<td>CLIENT NAME</td>
		<td></td>
		<td>Name :</td>
		<td></td>
		<td>Employee No.</td>
		<td></td>
	  </tr>
	  <tr>
		<td></td>
		<td></td>
		<td>Location:</td>
		<td></td>
		<td></td>
		<td></td>
	  </tr>
	  <tr>
		<td></td>
		<td></td>
		<td>Position:</td>
		<td></td>
		<td>Hrs/wk:</td>
		<td></td>
	  </tr>
	  <tr>
		<td></td>
		<td></td>
		<td>Commencement Date:</td>
		<td></td>
		<td></td>
		<td></td>
	  </tr>
	  </table><br><br>
	  <p align="center">EMPLOYEE TIMESHEET</p>
	  <table>
		<tr>
		  <td>Pay Period #</td>
		</tr>
		<tr>
		  <td>NET HOURS CARRIED:</td>
		</tr>
		<tr>
		  <td>BALANCE FORWARD Y.T.D.:</td>
		</tr>
	  </table>
	  <br><br>
	  ';

	  if(isset($_GET['element'])) {
		$element = $_GET['element'];
		$element = implode(',', $element);
		$query_check_credentials = 'SELECT * FROM time_cards WHERE approv = "Y" AND time_cards_id IN ('.$element.')';
	  } else {
		$query_check_credentials = 'SELECT * FROM time_cards WHERE approv = "Y" AND time_cards_id = '.$_GET['time_cards_id'];
	  }


		$result = mysqli_query($dbc, $query_check_credentials);

	  $num_rows = mysqli_num_rows($result);
	  if($num_rows > 0) {
		while($row = mysqli_fetch_array( $result ))
			{
			  $type = $row['type_of_time'];
			  $hours = $row['total_hrs'];
			  $hour_final = array();

			  foreach($options_array as $key=>$value) {
				$hour_final[$key] = 0;
				if($type == $value) {
				  $hour_final[$key] = $hours;
				}
			  }

		  $html .= '<table border="1" cellpadding="2">
		<tr>
		  <td>DATE</td>
		  <td>FROM-TO</td>
		  <td>REGULAR  HOURS</td>
		  <td>EXTRA HOURS</td>
		  <td>RELIEF HOURS</td>
		  <td>SLEEP HOURS</td>
		  <td>SICK TIME ADJUSTMENT</td>
		  <td>SICK HRS. TAKEN</td>
		  <td>STAT HOURS</td>
		  <td>STAT.HRS. TAKEN</td>
		  <td>VACATION HOURS</td>
		  <td>VACATION HRS. TAKEN</td>
		</tr>
		<tr>
			<td nowrap>'.$row['date'].'</td>
			<td>'.$row['start_time'].'-'.$row['end_time'].'</td>
			<td>'.$hour_final[0].'</td>
			<td>'.$hour_final[1].'</td>
			<td>'.$hour_final[2].'</td>
			<td>'.$hour_final[3].'</td>
			<td>'.$hour_final[4].'</td>
			<td>'.$hour_final[5].'</td>
			<td>'.$hour_final[6].'</td>
			<td>'.$hour_final[7].'</td>
			<td>'.$hour_final[8].'</td>
			<td>'.$hour_final[9].'</td>
		  </tr>
		  </table>
		  ';

		  $html .= '<br><br><br><table border="0" cellpadding="1" width="100%">
			<tr>
			  <td width="30%">Comment:</td>
			  <td>'. htmlspecialchars_decode($row['comment_box']).'</td>
			</tr>
			<tr>
			  <td>Manager:</td>
			  <td>'.$row['manager_name'].'</td>
			</tr>
			<tr>
			  <td>Date:</td>
			  <td>'.$row['date_manager'].'</td>
			</tr>
			<tr>
			  <td>Manager Signature:</td>
			  <td><img src="download/manager_'.$row['time_cards_id'].'.png"></td>
			</tr>
			<tr>
			  <td>Staff:</td>
			  <td>'.get_staff($dbc, $row['coordinator_name']).'</td>
			</tr>
			<tr>
			  <td>Date:</td>
			  <td>'.$row['date_coordinator'].'</td>
			</tr>
			<tr>
			  <td>Coordinator Signature:</td>
			  <td><img src="download/staff_'.$row['time_cards_id'].'.png"></td>
			</tr>
		  </table><br><br>';


		}
	  }
  }

  $pdf->writeHTML($html, true, false, true, false, '');
  $pdf->lastPage();
  $pdf->Output('download/records.pdf', 'I');
  exit;
}

$value = $config['settings']['Choose Fields for Time Sheets Dashboard'];
$field_config = get_field_config($dbc, 'time_cards_dashboard');

?>
<div class="container triple-pad-bottom" id="timesheet_div">
	<div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="timesheet_iframe" src=""></iframe>
		</div>
	</div>
    <div class="row">
        <div class="col-md-12">

        <h1 class="">Reporting
        <?php
        if(config_visible_function_custom($dbc)) {
            echo '<a href="field_config.php?from_url=reporting.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
        }
        ?>
        </h1>

        <?php echo get_tabs('Reporting', $_GET['tab'], array('db' => $dbc, 'field' => $value['config_field'])); ?>
        <br><br>

        <?php include('../Timesheet/reporting_content.php'); ?>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>