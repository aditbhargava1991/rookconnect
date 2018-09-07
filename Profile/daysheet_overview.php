<?php
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
if (isset($_POST['add_manual'])) {
    $contactid = $_SESSION['contactid'];

// Stop all the timer
    $running_tickets = mysqli_fetch_all(mysqli_query($dbc, "SELECT tt.* FROM `ticket_timer` tt LEFT JOIN `tickets` ti ON tt.`ticketid` = ti.`ticketid` WHERE tt.`created_by` = '$contactid' AND tt.`start_timer_time` > 0 AND ti.`deleted` = 0 AND ti.`status` != 'Archive' AND tt.`deleted` = 0"),MYSQLI_ASSOC);
    foreach ($running_tickets as $running_ticket) {
    	$tickettimerid = $running_ticket['tickettimerid'];
    	if(empty($running_ticket['timer']) && empty($running_ticket['end_time'])) {
	    	$timer = gmdate('H:i:s', strtotime(date('Y-m-d H:i:s')) - $running_ticket['start_timer_time']);
	    	$end_time = date('g:i A');
		    $query_update_ticket = "UPDATE `ticket_timer` SET `end_time` = '$end_time', `timer` = '$timer' WHERE `tickettimerid` = '$tickettimerid'";
		    $result_update_ticket = mysqli_query($dbc, $query_update_ticket);
    	}
	    $query_update_ticket = "UPDATE `ticket_timer` SET `start_timer_time`='0' WHERE `tickettimerid` = '$tickettimerid'";
	    $result_update_ticket = mysqli_query($dbc, $query_update_ticket);
	}
//
    $sign = $_POST['output'];
    $img = sigJsonToImage($sign);
    imagepng($img, 'download/eod_'.$contactid.'.png');

    class MYPDF extends TCPDF {
        public function Header() {
            $this->SetFont('helvetica', '', 8);
            $header_text = '';
            //$this->writeHTMLCell(0, 0, '', '', $header_text, 0, 0, false, "L", "R",true);
        }

        // Page footer
        public function Footer() {
            // Position at 15 mm from bottom
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);

    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);

	$html_weekly = '<h2>'.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' - '.date('Y-m-d').'</h2>'; // Form nu heading

    $contactid = $_SESSION['contactid'];

    $get_total_estimate =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_estimate FROM day_overview WHERE type='Estimate' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
    $estimate = $get_total_estimate['total_estimate'];

    $get_total_quote =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_quote FROM day_overview WHERE	type='Quote' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
    $quote = $get_total_quote['total_quote'];

    $get_total_project =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_project FROM day_overview WHERE	type='Project' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
    $project = $get_total_project['total_project'];

    $get_total_ticket =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_ticket FROM day_overview WHERE	type='Ticket' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
    $ticket = $get_total_ticket['total_ticket'];

    $get_total_tasks =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_task FROM day_overview WHERE	type='Task' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
    $tasks = $get_total_tasks['total_task'];

    $get_total_communication =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_communication FROM day_overview WHERE	type='Communication' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
    $communications = $get_total_communication['total_communication'];

    $get_total_checklists =  mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_checklist FROM day_overview WHERE   type='Checklist' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
    $checklists = $get_total_checklists['total_checklist'];

    $get_total_workorders =  mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_workorder FROM day_overview WHERE   type='Work Order' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
    $workorders = $get_total_workorders['total_workorder'];

    $get_total_meetings = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_meeting FROM day_overview WHERE type='Meeting' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
    $meetings = $get_total_meetings['total_meeting'];

    if($estimate > 0) {
        $html_weekly .= 'Estimate';

        $est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Estimate' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
        $html_weekly .= '<ul>';
        while($est_row = mysqli_fetch_array( $est )) {
            $html_weekly .= '<li>'.$est_row['description'].'</li>';
        }
        $html_weekly .= '</ul>';
    }

    if($quote > 0) {
        $html_weekly .= 'Quote';

        $est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Quote' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
        $html_weekly .= '<ul>';
        while($est_row = mysqli_fetch_array( $est )) {
            $html_weekly .= '<li>'.$est_row['description'].'</li>';
        }
        $html_weekly .= '</ul>';
    }

    if($project > 0) {
        $html_weekly .= 'Project';

        $est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Project' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
        $html_weekly .= '<ul>';
        while($est_row = mysqli_fetch_array( $est )) {
            $html_weekly .= '<li>'.$est_row['description'].'</li>';
        }
        $html_weekly .= '</ul>';
    }

    if($tasks > 0) {
        $html_weekly .= 'Task';

        $est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Task' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
        $html_weekly .= '<ul>';
        while($est_row = mysqli_fetch_array( $est )) {
            $html_weekly .= '<li>'.$est_row['description'].'</li>';
        }
        $html_weekly .= '</ul>';
    }

    if($communications > 0) {
        $html_weekly .= 'Communication';

        $est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Communication' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
        $html_weekly .= '<ul>';
        while($est_row = mysqli_fetch_array( $est )) {
            $html_weekly .= '<li>'.$est_row['description'].'</li>';
        }
        $html_weekly .= '</ul>';
    }

    if($checklists > 0) {
        $html_weekly .= 'Checklist';

        $est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Checklist' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
        $html_weekly .= '<ul>';
        while($est_row = mysqli_fetch_array( $est )) {
            $html_weekly .= '<li>'.$est_row['description'].'</li>';
        }
        $html_weekly .= '</ul>';
    }

    if($workorders > 0) {
        $html_weekly .= 'Work Order';

        $est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Work Order' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
        $html_weekly .= '<ul>';
        while($est_row = mysqli_fetch_array( $est )) {
            $html_weekly .= '<li>'.$est_row['description'].'</li>';
        }
        $html_weekly .= '</ul>';
    }

    if($meetings > 0) {
        $html_weekly .= 'Meetings';

        $est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Meeting' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
        $html_weekly .= '<ul>';
        while($est_row = mysqli_fetch_array( $est )) {
            $html_weekly .= '<li>'.$est_row['description'].'</li>';
        }
        $html_weekly .= '</ul>';
    }

    $query_check_credentials = "SELECT * FROM email_communication_timer WHERE created_by='$contactid' AND DATE(created_date) = CURDATE()";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= 'Communication Time Tracking<br />';
        $html_weekly .= '<table class="table table-bordered" border="1" cellpadding="2">
        <tr class="hidden-xs hidden-sm">
        <th>Communication #</th>
        <th>Type</th>
        <th>Time</th>
        </tr>';
        $times = array();
        while($row = mysqli_fetch_array($result)) {
            $html_weekly .= '<tr>';
            $by = $row['created_by'];
            $html_weekly .= '<td data-title="Schedule">'.$row['communication_id'].'</td>';
            $html_weekly .= '<td data-title="Schedule">'.$row['timer_type'].'</td>';
            $html_weekly .= '<td data-title="Schedule">'.$row['start_time'].' - '.$row['end_time'].'</td>';
            $html_weekly .= '</tr>';
            $times[] = $row['timer'];
        }
        $html_weekly .= '</table><br /><br />';
    }

    $query_check_credentials = "SELECT * FROM ticket_timer WHERE created_by='$contactid' AND DATE(created_date) = CURDATE() AND `deleted` = 0";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= TICKET_NOUN.' Time Tracking<br />';
        $html_weekly .= '<table class="table table-bordered" border="1" cellpadding="2">
        <tr class="hidden-xs hidden-sm">
        <th>'.TICKET_NOUN.'#</th>
        <th>Type</th>
        <th>Time</th>
        </tr>';
        $times = array();
        while($row = mysqli_fetch_array($result)) {
            $html_weekly .= '<tr>';
            $by = $row['created_by'];
            $html_weekly .= '<td data-title="Schedule">'.$row['ticketid'].'</td>';
            $html_weekly .= '<td data-title="Schedule">'.$row['timer_type'].'</td>';
            $html_weekly .= '<td data-title="Schedule">'.$row['start_time'].' - '.$row['end_time'].'</td>';
            $html_weekly .= '</tr>';
            $times[] = $row['timer'];
        }
        $html_weekly .= '</table><br /><br />';
    }

    $query_check_credentials = "SELECT * FROM ticket_comment WHERE created_by='$contactid' AND DATE(created_date) = CURDATE() AND type='day'";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);
    if($num_rows > 0) {
        $html_weekly .= TICKET_NOUN.' Day Tracking<br />';
        $html_weekly .= '<table class="table table-bordered" border="1" cellpadding="2">
        <tr class="hidden-xs hidden-sm">
        <th>'.TICKET_NOUN.'#</th>
        <th>Note</th>
        </tr>';
        $times = array();
        while($row = mysqli_fetch_array($result)) {
            $html_weekly .= '<tr>';
            $html_weekly .= '<td data-title="Schedule">'.$row['ticketid'].'</td>';
            $html_weekly .= '<td data-title="Schedule">'.html_entity_decode($row['comment']).'</td>';
            $html_weekly .= '</tr>';
        }
        $html_weekly .= '</table><br /><br />';
    }

    $query_check_credentials = "SELECT * FROM tasklist WHERE contactid='$contactid' AND ticketid IS NOT NULL AND (DATE(created_date) = CURDATE() OR DATE(task_tododate) = CURDATE())";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= TICKET_NOUN.' Tasklist<br />';

        $html_weekly .= '<table class="table table-bordered" border="1" cellpadding="2">';
        $html_weekly .= "<tr class='hidden-xs hidden-sm'>
        <th>Contact</th>
        <th>Task</th>
        <th>Created Date</th>
        <th>To Do Date</th>
        <th>Work Time</th>
        <th>Status</th>
        ";
        $html_weekly .= "</tr>";
        while($row = mysqli_fetch_array( $result ))
        {
            $tasklistid = $row['tasklistid'];
            $html_weekly .= "<tr>";

            $clientid = $row['clientid'];
            $get_client = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name FROM contacts WHERE contactid='$clientid'"));
            $name = decryptIt($get_client['name']);

            $html_weekly .= '<td data-title="Business Name">' . $name . '</td>';
            $html_weekly .= '<td data-title="Name">' . html_entity_decode($row['task']) . '</td>';
            $html_weekly .= '<td data-title="Office Phone">' . $row['created_date'] . '</td>';
            $html_weekly .= '<td data-title="Office Phone">' . $row['task_tododate'] . '</td>';
            $html_weekly .= '<td data-title="Office Phone">' . $row['work_time'] . '</td>';
            $html_weekly .= '<td data-title="Office Phone">' . $row['status'] . '</td>';
            $html_weekly .= "</tr>";
        }

        $html_weekly .= '</table><br /><br />';
    }

    $query_check_credentials = "SELECT * FROM workorder_timer WHERE created_by='$contactid' AND DATE(created_date) = CURDATE()";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= 'Workorder Time Tracking<br />';

        $html_weekly .= '<table class="table table-bordered" border="1" cellpadding="2">
        <tr class="hidden-xs hidden-sm">
        <th>Workorder#</th>
        <th>Type</th>
        <th>Time</th>
        </tr>';
        $times = array();
        while($row = mysqli_fetch_array($result)) {
            $html_weekly .= '<tr>';
            $by = $row['created_by'];
            $html_weekly .= '<td data-title="Schedule">'.$row['workorderid'].'</td>';
            $html_weekly .= '<td data-title="Schedule">'.$row['timer_type'].'</td>';
            $html_weekly .= '<td data-title="Schedule">'.$row['start_time'].' - '.$row['end_time'].'</td>';
            $html_weekly .= '</tr>';
            $times[] = $row['timer'];
        }
        $html_weekly .= '</table><br /><br />';
    }

    /*$query_check_credentials = "SELECT * FROM tasklist WHERE contactid='$contactid' AND workorderid IS NOT NULL AND (DATE(created_date) = CURDATE() OR DATE(task_tododate) = CURDATE())";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= 'Workorder Tasklist';
        $html_weekly .= "<table class='table table-bordered'>";
        $html_weekly .= "<tr class='hidden-xs hidden-sm'>
        <th>Contact</th>
        <th>Task</th>
        <th>Created Date</th>
        <th>To Do Date</th>
        <th>Work Time</th>
        <th>Status</th>
        ";
        $html_weekly .= "</tr>";
        while($row = mysqli_fetch_array( $result ))
        {
            $tasklistid = $row['tasklistid'];
            $html_weekly .= "<tr>";

            $clientid = $row['clientid'];
            $get_client = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name FROM contacts WHERE contactid='$clientid'"));
            $name = decryptIt($get_client['name']);

            $html_weekly .= '<td data-title="Business Name">' . $name . '</td>';
            $html_weekly .= '<td data-title="Name">' . html_entity_decode($row['task']) . '</td>';
            $html_weekly .= '<td data-title="Office Phone">' . $row['created_date'] . '</td>';
            $html_weekly .= '<td data-title="Office Phone">' . $row['task_tododate'] . '</td>';
            $html_weekly .= '<td data-title="Office Phone">' . $row['work_time'] . '</td>';
            $html_weekly .= '<td data-title="Office Phone">' . $row['status'] . '</td>';
            $html_weekly .= "</tr>";
        }
        $html_weekly .= '</table>';
    }*/

    $query_check_credentials = "SELECT * FROM `tasklist_time` timer LEFT JOIN `tasklist` tasks ON timer.`tasklistid`=tasks.`tasklistid` WHERE `timer_date`=CURDATE()";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= 'Tasks<br />';
        $html_weekly .= '<table class="table table-bordered" border="1" cellpadding="2">';
        $html_weekly .= "<tr class='hidden-xs hidden-sm'>
        <th>Project #</th>
        <th>Task</th>
        <th>Work Date</th>
        <th>Time Spent</th>
        ";
        $html_weekly .= "</tr>";
        while($row = mysqli_fetch_array( $result ))
        {
            $tasklistid = $row['tasklistid'];
            $html_weekly .= "<tr>";
            $html_weekly .= '<td data-title="Project #">' . $row['projectid'] . '</td>';
            $html_weekly .= '<td data-title="Task">' . html_entity_decode($row['heading']) . '</td>';
            $html_weekly .= '<td data-title="Work Date">' . $row['timer_date'] . '</td>';
            $html_weekly .= '<td data-title="Time Spent">' . $row['work_time'] . '</td>';
            $html_weekly .= "</tr>";
        }
        $html_weekly .= '</table><br /><br />';
    }

    $query_check_credentials = "SELECT list.`checklist_name`, checklist.`checklist`, timer.`timer_date`, timer.`work_time` FROM `checklist_name_time` timer LEFT JOIN `checklist_name` checklist ON timer.`checklist_id`=checklist.`checklistnameid` LEFT JOIN `checklist` list ON checklist.`checklistid`=list.`checklistid` WHERE `timer_date`=CURDATE()";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= 'Checklists<br />';
        $html_weekly .= '<table class="table table-bordered" border="1" cellpadding="2">';
        $html_weekly .= "<tr class='hidden-xs hidden-sm'>
        <th>Checklist</th>
        <th>Checklist Item</th>
        <th>Work Date</th>
        <th>Time Spent</th>
        ";
        $html_weekly .= "</tr>";
        while($row = mysqli_fetch_array( $result ))
        {
            $tasklistid = $row['tasklistid'];
            $html_weekly .= "<tr>";
            $html_weekly .= '<td data-title="Checklist">' . $row['checklist_name'] . '</td>';
            $html_weekly .= '<td data-title="Checklist Item">' . html_entity_decode($row['checklist']) . '</td>';
            $html_weekly .= '<td data-title="Work Date">' . $row['timer_date'] . '</td>';
            $html_weekly .= '<td data-title="Time Spent">' . $row['work_time'] . '</td>';
            $html_weekly .= "</tr>";
        }
        $html_weekly .= '</table><br /><br />';
    }

    $query_check_credentials = "SELECT * FROM workorder_comment WHERE created_by='$contactid' AND DATE(created_date) = CURDATE() AND type='day'";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= 'Workorder Day Tracking<br />';

        $html_weekly .= '<table class="table table-bordered" border="1" cellpadding="2">
        <tr class="hidden-xs hidden-sm">
        <th>Workorder#</th>
        <th>Note</th>
        </tr>';
        $times = array();
        while($row = mysqli_fetch_array($result)) {
            $html_weekly .= '<tr>';
            $html_weekly .= '<td data-title="Schedule">'.$row['workorderid'].'</td>';
            $html_weekly .= '<td data-title="Schedule">'.html_entity_decode($row['comment']).'</td>';
            $html_weekly .= '</tr>';
        }
        $html_weekly .= '</table><br /><br />';
    }

    $query_check_credentials = "SELECT amt.* FROM `agenda_meeting_timer` amt LEFT JOIN `agenda_meeting` am ON amt.`agendameetingid` = am.`agendameetingid` WHERE DATE(amt.`created_date`) = CURDATE() AND (CONCAT(',',am.`businesscontactid`,',') LIKE '%,$contactid,%' OR CONCAT(',',am.`companycontactid`,',') LIKE '%,$contactid,%')";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= 'Meetings Time Tracking<br />';

        $html_weekly .= '<table class="table table-bordered" border="1" cellpadding="2">
        <tr class="hidden-xs hidden-sm">
        <th>Meeting#</th>
        <th>Type</th>
        <th>Time</th>
        </tr>';
        $times = array();
        while($row = mysqli_fetch_array($result)) {
            $html_weekly .= '<tr>';
            $html_weekly .= '<td data-title="Meeting#">'.$row['agendameetingid'].'</td>';
            $html_weekly .= '<td data-title="Type">'.$row['timer_type'].'</td>';
            $html_weekly .= '<td data-title="Time">'.$row['start_time'].' - '.$row['end_time'].'</td>';
            $html_weekly .= '</tr>';
            $times[] = $row['timer'];
        }
        $html_weekly .= '</table><br /><br />';
    }

    $html_weekly .= '<img src="download/eod_'.$contactid.'.png" width="150" height="70" border="0" alt="">';

    $pdf->writeHTML($html_weekly, true, false, true, false, '');

    // avs_near_miss = form name
    $pdf->Output('download/Day_'.date('Y-m-d').'-'.$_SESSION['contactid'].'.pdf', 'F');
    $today_date = date('Y-m-d');

    $query_insert_vendor = "INSERT INTO `eod_daysheet` (`contactid`, `date`) VALUES ('$contactid', '$today_date')";
    $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);

    unlink("download/eod_".$contactid.".png");

    if(FOLDER_NAME == 'daysheet') {
        echo '<script type="text/javascript"> window.location.replace("../Daysheet/daysheet.php"); </script>';
    } else {
        echo '<script type="text/javascript"> window.location.replace("../Profile/daysheet.php"); </script>';
    }
}
?>


<?php
$contactid = $_SESSION['contactid'];

$get_total_estimate =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_estimate FROM day_overview WHERE type='Estimate' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
$estimate = $get_total_estimate['total_estimate'];

$get_total_quote =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_quote FROM day_overview WHERE	type='Quote' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
$quote = $get_total_quote['total_quote'];

$get_total_project =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_project FROM day_overview WHERE	type='Project' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
$project = $get_total_project['total_project'];

$get_total_ticket =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_ticket FROM day_overview WHERE	type='Ticket' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
$ticket = $get_total_ticket['total_ticket'];

$get_total_task =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_task FROM day_overview WHERE	type='Task' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
$tasks = $get_total_task['total_task'];

$get_total_communication =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_communication FROM day_overview WHERE	type='Communication' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
$communications = $get_total_communication['total_communication'];

$get_total_checklists =  mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_checklist FROM day_overview WHERE   type='Checklist' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
$checklists = $get_total_checklists['total_checklist'];

$get_total_workorders =  mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_workorder FROM day_overview WHERE   type='Work Order' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
$workorders = $get_total_workorders['total_workorder'];

$get_total_meetings = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(overviewid) AS total_meeting FROM day_overview WHERE type='Meeting' AND contactid='$contactid' AND DATE(today_date) = CURDATE()"));
$meetings = $get_total_meetings['total_meeting'];

?>

<div class="col-sm-12 main-screen-details gap-top">
    <a href="daysheet.php" class="btn brand-btn">Back to Dashboard</a>
    <?php if($estimate > 0) { ?>
        <h4>Estimates</h4>
        	<?php
        	$est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Estimate' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
        	echo '<ul>';
        	while($est_row = mysqli_fetch_array( $est )) {
        		echo '<li>'.$est_row['description'].'</li>';
        	}
        	echo '</ul>';
        	?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php if($quote > 0) { ?>
        <h4>Quotes</h4>
    		<?php
    		$est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Quote' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
    		echo '<ul>';
    		while($est_row = mysqli_fetch_array( $est )) {
    			echo '<li>'.$est_row['description'].'</li>';
    		}
    		echo '</ul>';
    		?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php if($project > 0) { ?>
        <h4><?= PROJECT_TILE ?></h4>
    		<?php
    		$est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Project' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
    		echo '<ul>';
    		while($est_row = mysqli_fetch_array( $est )) {
    			echo '<li>'.$est_row['description'].'</li>';
    		}
    		echo '</ul>';
    		?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php if($ticket > 0) { ?>
        <h4><?= TICKET_TILE ?></h4>
    		<?php
    		$est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Ticket' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
    		echo '<ul>';
    		while($est_row = mysqli_fetch_array( $est )) {
    			echo '<li>'.$est_row['description'].'</li>';
    		}
    		echo '</ul>';
    		?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php if($tasks > 0) { ?>
        <h4><?= TASK_TILE ?></h4>
    		<?php
    		$est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Task' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
    		echo '<ul>';
    		while($est_row = mysqli_fetch_array( $est )) {
    			echo '<li>'.$est_row['description'].'</li>';
    		}
    		echo '</ul>';
    		?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php if($communications > 0) { ?>
        <h4>Communication</h4>
            <?php
            $est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Communication' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
            echo '<ul>';
            while($est_row = mysqli_fetch_array( $est )) {
                echo '<li>'.$est_row['description'].'</li>';
            }
            echo '</ul>';
            ?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php if($checklists > 0) { ?>
        <h4>Checklist</h4>
            <?php
            $est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Checklist' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
            echo '<ul>';
            while($est_row = mysqli_fetch_array( $est )) {
                echo '<li>'.$est_row['description'].'</li>';
            }
            echo '</ul>';
            ?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php if($workorders > 0) { ?>
        <h4>Work Order</h4>
            <?php
            $est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Work Order' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
            echo '<ul>';
            while($est_row = mysqli_fetch_array( $est )) {
                echo '<li>'.$est_row['description'].'</li>';
            }
            echo '</ul>';
            ?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php if($meetings > 0) { ?>
        <h4>Meetings</h4>
            <?php
            $est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Meeting' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
            echo '<ul>';
            while($est_row = mysqli_fetch_array( $est )) {
                echo '<li>'.$est_row['description'].'</li>';
            }
            echo '</ul>';
            ?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php
    	$query_check_credentials = "SELECT * FROM email_communication_timer WHERE created_by='$contactid' AND DATE(created_date) = CURDATE()";
    	$result = mysqli_query($dbc, $query_check_credentials);
    	$num_rows = mysqli_num_rows($result);
    ?>
    <?php if($num_rows > 0) { ?>
        <h4>Communication Time Tracking</h4>
    		<?php
    			echo "<table class='table table-bordered'>
    			<tr class='hidden-xs hidden-sm'>
    			<th>Communication#</th>
    			<th>Type</th>
    			<th>Time</th>
    			</tr>";
    			$times = array();
    			while($row = mysqli_fetch_array($result)) {
    				echo '<tr>';
    				$by = $row['created_by'];
    				echo '<td data-title="Schedule">'.$row['communication_id'].'</td>';
    				echo '<td data-title="Schedule">'.$row['timer_type'].'</td>';
    				echo '<td data-title="Schedule">'.$row['start_time'].' - '.$row['end_time'].'</td>';
    				echo '</tr>';
    				//$total_time += strtotime($row['timer']);
    				$times[] = $row['timer'];
    			}
    			echo '</table>';
    			?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php
    	$query_check_credentials = "SELECT * FROM ticket_timer WHERE created_by='$contactid' AND DATE(created_date) = CURDATE() AND `deleted` = 0";
    	$result = mysqli_query($dbc, $query_check_credentials);
    	$num_rows = mysqli_num_rows($result);
    ?>
    <?php if($num_rows > 0) { ?>
        <h4><?= TICKET_TILE ?> Time Tracking</h4>
    		<?php
    			echo "<table class='table table-bordered'>
    			<tr class='hidden-xs hidden-sm'>
    			<th>".TICKET_NOUN."#</th>
    			<th>Type</th>
    			<th>Time</th>
    			</tr>";
    			$times = array();
    			while($row = mysqli_fetch_array($result)) {
    				echo '<tr>';
    				$by = $row['created_by'];
    				echo '<td data-title="Schedule">'.$row['ticketid'].'</td>';
    				echo '<td data-title="Schedule">'.$row['timer_type'].'</td>';
    				echo '<td data-title="Schedule">'.$row['start_time'].' - '.$row['end_time'].'</td>';
    				echo '</tr>';
    				//$total_time += strtotime($row['timer']);
    				$times[] = $row['timer'];
    			}
    			echo '</table>';
    			?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php
    	$query_check_credentials = "SELECT * FROM ticket_comment WHERE created_by='$contactid' AND DATE(created_date) = CURDATE() AND type='day'";
    	$result = mysqli_query($dbc, $query_check_credentials);
    	$num_rows = mysqli_num_rows($result);
    ?>
    <?php if($num_rows > 0) { ?>
        <h4><?= TICKET_NOUN ?> Day Tracking</h4>
    		<?php
    			echo "<table class='table table-bordered'>
    			<tr class='hidden-xs hidden-sm'>
    			<th>".TICKET_NOUN."#</th>
    			<th>Note</th>
    			</tr>";
    			$times = array();
    			while($row = mysqli_fetch_array($result)) {
    				echo '<tr>';
    				echo '<td data-title="Schedule">'.$row['ticketid'].'</td>';
    				echo '<td data-title="Schedule">'.html_entity_decode($row['comment']).'</td>';
    				echo '</tr>';
    			}
    			echo '</table>';
    			?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php
    	$query_check_credentials = "SELECT * FROM tasklist WHERE contactid='$contactid' AND ticketid IS NOT NULL AND (DATE(created_date) = CURDATE() OR DATE(task_tododate) = CURDATE())";
    	$result = mysqli_query($dbc, $query_check_credentials);
    	$num_rows = mysqli_num_rows($result);
    ?>

    <?php if($num_rows > 0) { ?>
        <h4>Task List</h4>
    		<?php
    		echo "<table class='table table-bordered'>";
    		echo "<tr class='hidden-xs hidden-sm'>
    		<th>Contact</th>
    		<th>Task</th>
    		<th>Created Date</th>
    		<th>To Do Date</th>
    		<th>Work Time</th>
    		<th>Status</th>
    		";
    		echo "</tr>";
    		while($row = mysqli_fetch_array( $result ))
    		{
    			$contactid = $row['contactid'];
    			$tasklistid = $row['tasklistid'];
    			echo "<tr>";

    			$clientid = $row['clientid'];
    			$get_client = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name FROM contacts WHERE contactid='$clientid'"));
    			$name = decryptIt($get_client['name']);

    			echo '<td data-title="Business Name">' . $name . '</td>';
    			echo '<td data-title="Name">' . html_entity_decode($row['task']) . '</td>';
    			echo '<td data-title="Office Phone">' . $row['created_date'] . '</td>';
    			echo '<td data-title="Office Phone">' . $row['task_tododate'] . '</td>';
    			echo '<td data-title="Office Phone">' . $row['work_time'] . '</td>';
    			echo '<td data-title="Office Phone">' . $row['status'] . '</td>';
    			echo "</tr>";
    		}

    		echo '</table>';
    		?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php
    	$query_check_credentials = "SELECT * FROM workorder_timer WHERE created_by='$contactid' AND DATE(created_date) = CURDATE()";
    	$result = mysqli_query($dbc, $query_check_credentials);
    	$num_rows = mysqli_num_rows($result);
    ?>
    <?php if($num_rows > 0) { ?>
        <h4>Work Order Time Tracking</h4>
    		<?php
    			echo "<table class='table table-bordered'>
    			<tr class='hidden-xs hidden-sm'>
    			<TH>Workorder#</th>
    			<th>Type</th>
    			<th>Time</th>
    			</tr>";
    			$times = array();
    			while($row = mysqli_fetch_array($result)) {
    				echo '<tr>';
    				$by = $row['created_by'];
    				echo '<td data-title="Schedule">'.$row['workorderid'].'</td>';
    				echo '<td data-title="Schedule">'.$row['timer_type'].'</td>';
    				echo '<td data-title="Schedule">'.$row['start_time'].' - '.$row['end_time'].'</td>';
    				echo '</tr>';
    				//$total_time += strtotime($row['timer']);
    				$times[] = $row['timer'];
    			}
    			echo '</table>';
    			?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php
    	$query_check_credentials = "SELECT * FROM tasklist WHERE contactid='$contactid' AND workorderid IS NOT NULL AND (DATE(created_date) = CURDATE() OR DATE(task_tododate) = CURDATE())";
    	$result = mysqli_query($dbc, $query_check_credentials);
    	$num_rows = mysqli_num_rows($result);
    ?>

    <?php if($num_rows > 0) { ?>
        <h4>Work Order Task List</h4>
    		<?php
    		echo "<table class='table table-bordered'>";
    		echo "<tr class='hidden-xs hidden-sm'>
    		<th>Contact</th>
    		<th>Task</th>
    		<th>Created Date</th>
    		<th>To Do Date</th>
    		<th>Work Time</th>
    		<th>Status</th>
    		";
    		echo "</tr>";
    		while($row = mysqli_fetch_array( $result ))
    		{
    			$contactid = $row['contactid'];
    			$tasklistid = $row['tasklistid'];
    			echo "<tr>";

    			$clientid = $row['clientid'];
    			$get_client = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name FROM contacts WHERE contactid='$clientid'"));
    			$name = decryptIt($get_client['name']);

    			echo '<td data-title="Business Name">' . $name . '</td>';
    			echo '<td data-title="Name">' . html_entity_decode($row['task']) . '</td>';
    			echo '<td data-title="Office Phone">' . $row['created_date'] . '</td>';
    			echo '<td data-title="Office Phone">' . $row['task_tododate'] . '</td>';
    			echo '<td data-title="Office Phone">' . $row['work_time'] . '</td>';
    			echo '<td data-title="Office Phone">' . $row['status'] . '</td>';
    			echo "</tr>";
    		}

    		echo '</table>';
    		?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php
    	$query_check_credentials = "SELECT * FROM workorder_comment WHERE created_by='$contactid' AND DATE(created_date) = CURDATE() AND type='day'";
    	$result = mysqli_query($dbc, $query_check_credentials);
    	$num_rows = mysqli_num_rows($result);
    ?>
    <?php if($num_rows > 0) { ?>
        <h4>Work Order Day Tracking</h4>
    		<?php
    			echo "<table class='table table-bordered'>
    			<tr class='hidden-xs hidden-sm'>
    			<th>Workorder#</th>
    			<th>Note</th>
    			</tr>";
    			$times = array();
    			while($row = mysqli_fetch_array($result)) {
    				echo '<tr>';
    				echo '<td data-title="Schedule">'.$row['workorderid'].'</td>';
    				echo '<td data-title="Schedule">'.html_entity_decode($row['comment']).'</td>';
    				echo '</tr>';
    			}
    			echo '</table>';
    			?>
        <div class="clearfix"></div>
    <?php } ?>

    <?php
        $query_check_credentials = "SELECT amt.* FROM `agenda_meeting_timer` amt LEFT JOIN `agenda_meeting` am ON amt.`agendameetingid` = am.`agendameetingid` WHERE DATE(amt.`created_date`) = CURDATE() AND (CONCAT(',',am.`businesscontactid`,',') LIKE '%,$contactid,%' OR CONCAT(',',am.`companycontactid`,',') LIKE '%,$contactid,%')";
        $result = mysqli_query($dbc, $query_check_credentials);
        $num_rows = mysqli_num_rows($result);
    ?>
    <?php if($num_rows > 0) { ?>
        <h4>Meetings Time Tracking</h4>
            <?php
                echo "<table class='table table-bordered'>
                <tr class='hidden-xs hidden-sm'>
                <th>Meeting#</th>
                <th>Type</th>
                <th>Time</th>
                </tr>";
                $times = array();
                while($row = mysqli_fetch_array($result)) {
                    echo '<tr>';
                    echo '<td data-title="Meeting#">'.$row['agendameetingid'].'</td>';
                    echo '<td data-title="Type">'.$row['timer_type'].'</td>';
                    echo '<td data-title="Time">'.$row['start_time'].' - '.$row['end_time'].'</td>';
                    echo '</tr>';
                    $times[] = $row['timer'];
                }
                echo '</table>';
                ?>
        <div class="clearfix"></div>
    <?php } ?>

    <div class="form-group">
    	<label for="first_name[]" class="col-sm-4 control-label">
    		<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="In order to sign off for your daily work, sign within this box and click Submit."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
    		End of Day Signature:
    	</label>
    	<div class="col-sm-8">
    		<?php include ('../phpsign/sign.php'); ?>
    	</div>
    </div>

    <div class="clearfix"></div>

    <div class="form-group col-sm-6">
    	<div class="col-sm-4">
    		&nbsp;
    		<!--<a href="../home.php" class="btn brand-btn pull-right">Back</a>
    		<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
    	</div>
    	<div class="col-sm-8 pull-right">
    		<button type="submit" name="add_manual" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
    		<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save your Planner."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
    	</div>
    </div>
    <br><br><br><br>
    <div class="panel-group" id="accordion3" style="width:85%;margin-left:5%">
        <div>
            <span class="popover-examples pull-left pad-right"><a data-toggle="tooltip" data-placement="top" title="Click to view all Planners relevant to you."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <h4><a data-toggle="collapse" data-parent="#accordion3" href="#collapse_history">History</a></h4>
            <div class="clearfix"></div>
        </div>
    <div id="collapse_history" class="panel-collapse">
      <div class="panel-body">
      <?php
        //DATE(NOW())
        $today = date('Y-m-d');
        $query_check_credentials = "SELECT * FROM day_overview WHERE today_date = '".$today."' order by today_date DESC";
        $result = mysqli_query($dbc, $query_check_credentials);
        $num_rows = mysqli_num_rows($result);
      ?>
      <?php
        echo "<table class='table table-bordered'>
        <tr>
        <th>Action/Work</th>
        <th>Business/Project/Job/Tile</th>
        <th>Description</th>
        <th>Time-Actual</th>
        <th>Time-Manual</th>
        <th>Time-Summary</th>
        </tr>";
        $times = array();
        $final = '00:00:00';
        while($row = mysqli_fetch_array($result)) {
          $ticketNumber = '';
          if(strpos($row['description'], 'Created Ticket') !== false) {
            $action = 'Add';
            $ticketNumber = substr($row['description'], strpos($row['description'], "#") + 1, strpos($row['description'], "-") - 1);
          }
          elseif(strpos($row['description'], 'Updated Ticket') !== false) {
            $action = 'Add';
            $ticketNumber = substr($row['description'], strpos($row['description'], "#") + 1, strpos($row['description'], "-") - 1);
          }
          elseif(strpos($row['description'], 'Edited') !== false)
            $action = 'Edit';
          elseif(strpos($row['description'], 'Approved') !== false)
            $action = 'Approved';
          elseif(strpos($row['description'], 'Archive') !== false)
            $action = 'Archive';
          $hours = '00';
          $minutes = '00';
          $seconds = '00';
          $spent_time = '';

          if($ticketNumber != '') {
            $query_check_credentials1 = "SELECT * FROM ticket_timer WHERE ticketid='$ticketNumber' AND `deleted` = 0 ORDER BY tickettimerid DESC";
            $result1 = mysqli_query($dbc, $query_check_credentials1);
            $num_rows1 = mysqli_num_rows($result1);
            if($num_rows1 > 0) {
              $times = array();
              $add_times = 0;
              while($row1 = mysqli_fetch_array($result1)) {
                //$total_time += strtotime($row['timer']);

                if($row1['end_time'] != '' && $row1['timer_type'] == 'Work') {
                  $to_time = strtotime($row1['start_time']);
                  $from_time = strtotime($row1['end_time']);
                  $times[] = round(abs($to_time - $from_time) / 60,2);
                  $add_times += round(abs($to_time - $from_time),2);
                }
              }

              $hours = floor($add_times / 3600);
              $minutes = floor($add_times % 3600 / 60);
              $seconds = $add_times % 60;
              if($hours <= 10) {
                $hours = '0' . $hours;
              }
              if($minutes <= 10) {
                $minutes = '0' . $minutes;
              }
              if($seconds <= 10) {
                $seconds = '0' . $seconds;
              }
            }

            $actual_time = $hours.':'.$minutes.':'.$seconds;
            $get_ticket1 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT spent_time FROM tickets WHERE ticketid='$ticketNumber'"));
            $spent_time = $get_ticket1['spent_time'];
            $secs = strtotime($spent_time)-strtotime("00:00:00");
            $summary = date("H:i:s",strtotime($actual_time)+$secs);
            $final_temp = strtotime($final)-strtotime("00:00:00");
            $final = date("H:i:s",strtotime($summary)+$final_temp);
          }
          echo '<td data-title="Staff">'.$action.'</td>';
          echo '<td data-title="Staff">'.$row['type'].'</td>';
          echo '<td data-title="Staff">'.$row['description'].'</td>';
          echo '<td data-title="Staff">'.$actual_time.'</td>';
          echo '<td data-title="Staff">'.$spent_time.'</td>';
          echo '<td data-title="Staff">'.$summary.'</td>';
          echo '</tr>';
        }
        echo '<tr>';
        echo '<td colspan="5"><span style="float:right">Total Time</td></td>';
        echo '<td>'.$final.'</td></tr>';

        echo '</table>';
        ?>
      </div>
      </div>
    </div>

    <div class="clearfix"></div>


    <!-- <h4>Day Sheet</h4>
    <?php
    	$query_check_credentials = "SELECT * FROM eod_daysheet WHERE date >= DATE(NOW()) - INTERVAL 7 DAY ORDER BY date DESC";
    	$result = mysqli_query($dbc, $query_check_credentials);
    	$num_rows = mysqli_num_rows($result);
    ?>
    <?php
    	echo "<table class='table table-bordered'>
    	<tr class='hidden-xs hidden-sm'>
    	<th>Staff</th>
    	<th>Date</th>
    	<th>View</th>
    	</tr>";
    	$times = array();
    	while($row = mysqli_fetch_array($result)) {
    		echo '<tr>';
    		echo '<td data-title="Staff">'.get_staff($dbc, $row['contactid']).'</td>';
    		echo '<td data-title="Date">'.$row['date'].'</td>';
            $pdf_url = file_exists('../Daysheet/download/Day_'.$row['date'].'-'.$row['contactid'].'.pdf') ? '../Daysheet/download/Day_'.$row['date'].'-'.$row['contactid'].'.pdf' : 'download/Day_'.$row['date'].'-'.$row['contactid'].'.pdf';
    		echo '<td data-title="View"><a target="_blank" href="'.$pdf_url.'"><img src="'.WEBSITE_URL.'/img/pdf.png" width="'.$img_width.'" height="'.$img_height.'" border="0" alt=""></a></td>';
    		echo '</tr>';
    	}
    	echo '</table>';
    	?>-->
    <div class="clearfix"></div>
</div>
