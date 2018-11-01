<?php
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
if (isset($_POST['add_manual'])) {
    $sign = $_POST['output'];
    $img = sigJsonToImage($sign);
    $contactid = $_SESSION['contactid'];
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

    if($estimate > 0) {
        $html_weekly .= ESTIMATE_TILE;

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

    if($ticket > 0) {
        $html_weekly .= 'Ticket';

        $est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Ticket' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
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

    $query_check_credentials = "SELECT * FROM email_communication_timer WHERE created_by='$contactid' AND DATE(created_date) = CURDATE()";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= 'Communication Time Tracking';
        $html_weekly .= "<table class='table table-bordered'>
        <tr class='hidden-xs hidden-sm'>
        <th>Communication #</th>
        <th>Type</th>
        <th>Time</th>
        </tr>";
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
        $html_weekly .= '</table>';
    }

    $query_check_credentials = "SELECT * FROM ticket_timer WHERE created_by='$contactid' AND DATE(created_date) = CURDATE() AND `deleted` = 0";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= TICKET_NOUN.' Time Tracking';
        $html_weekly .= "<table class='table table-bordered'>
        <tr class='hidden-xs hidden-sm'>
        <th>".TICKET_NOUN."#</th>
        <th>Type</th>
        <th>Time</th>
        </tr>";
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
        $html_weekly .= '</table>';
    }

    $query_check_credentials = "SELECT * FROM ticket_comment WHERE created_by='$contactid' AND DATE(created_date) = CURDATE() AND type='day'";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);
    if($num_rows > 0) {
        $html_weekly .= TICKET_NOUN.' Day Tracking';
        $html_weekly .= "<table class='table table-bordered'>
        <tr class='hidden-xs hidden-sm'>
        <th>".TICKET_NOUN."#</th>
        <th>Note</th>
        </tr>";
        $times = array();
        while($row = mysqli_fetch_array($result)) {
            $html_weekly .= '<tr>';
            $html_weekly .= '<td data-title="Schedule">'.$row['ticketid'].'</td>';
            $html_weekly .= '<td data-title="Schedule">'.html_entity_decode($row['comment']).'</td>';
            $html_weekly .= '</tr>';
        }
        $html_weekly .= '</table>';
    }

    $query_check_credentials = "SELECT * FROM tasklist WHERE contactid='$contactid' AND ticketid IS NOT NULL AND (DATE(created_date) = CURDATE() OR DATE(task_tododate) = CURDATE())";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= TICKET_NOUN.' Tasklist';

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
    }

    $query_check_credentials = "SELECT * FROM workorder_timer WHERE created_by='$contactid' AND DATE(created_date) = CURDATE()";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= 'Workorder Time Tracking';

        $html_weekly .= "<table class='table table-bordered'>
        <tr class='hidden-xs hidden-sm'>
        <th>Workorder#</th>
        <th>Type</th>
        <th>Time</th>
        </tr>";
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
        $html_weekly .= '</table>';
    }

    $query_check_credentials = "SELECT * FROM tasklist WHERE contactid='$contactid' AND workorderid IS NOT NULL AND (DATE(created_date) = CURDATE() OR DATE(task_tododate) = CURDATE())";
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
    }

    $query_check_credentials = "SELECT * FROM `project_milestone_checklist_time` timer LEFT JOIN `project_milestone_checklist` checklist ON timer.`checklist_id`=checklist.`checklistid` WHERE `timer_date`=CURDATE()";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= 'Project Checklists';
        $html_weekly .= "<table class='table table-bordered'>";
        $html_weekly .= "<tr class='hidden-xs hidden-sm'>
        <th>Project #</th>
        <th>Checklist Item</th>
        <th>Work Date</th>
        <th>Time Spent</th>
        ";
        $html_weekly .= "</tr>";
        while($row = mysqli_fetch_array( $result ))
        {
            $tasklistid = $row['tasklistid'];
            $html_weekly .= "<tr>";
            $html_weekly .= '<td data-title="Project #">' . $row['projectid'] . '</td>';
            $html_weekly .= '<td data-title="Checklist Item">' . html_entity_decode($row['checklist']) . '</td>';
            $html_weekly .= '<td data-title="Work Date">' . $row['timer_date'] . '</td>';
            $html_weekly .= '<td data-title="Time Spent">' . $row['work_time'] . '</td>';
            $html_weekly .= "</tr>";
        }
        $html_weekly .= '</table>';
    }

    $query_check_credentials = "SELECT list.`checklist_name`, checklist.`checklist`, timer.`timer_date`, timer.`work_time` FROM `checklist_name_time` timer LEFT JOIN `checklist_name` checklist ON timer.`checklist_id`=checklist.`checklistnameid` LEFT JOIN `checklist` list ON checklist.`checklistid`=list.`checklistid` WHERE `timer_date`=CURDATE()";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= 'Checklists';
        $html_weekly .= "<table class='table table-bordered'>";
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
        $html_weekly .= '</table>';
    }

    $query_check_credentials = "SELECT * FROM workorder_comment WHERE created_by='$contactid' AND DATE(created_date) = CURDATE() AND type='day'";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        $html_weekly .= 'Workorder Day Tracking';

        $html_weekly .= "<table class='table table-bordered'>
        <tr class='hidden-xs hidden-sm'>
        <th>Workorder#</th>
        <th>Note</th>
        </tr>";
        $times = array();
        while($row = mysqli_fetch_array($result)) {
            $html_weekly .= '<tr>';
            $html_weekly .= '<td data-title="Schedule">'.$row['workorderid'].'</td>';
            $html_weekly .= '<td data-title="Schedule">'.html_entity_decode($row['comment']).'</td>';
            $html_weekly .= '</tr>';
        }
        $html_weekly .= '</table>';
    }

    $html_weekly .= '<img src="download/eod_'.$contactid.'.png" width="150" height="70" border="0" alt="">';

    $pdf->writeHTML($html_weekly, true, false, true, false, '');

    // avs_near_miss = form name
    $pdf->Output('download/Day_'.date('Y-m-d').'-'.$_SESSION['contactid'].'.pdf', 'F');
    $today_date = date('Y-m-d');

    $query_insert_vendor = "INSERT INTO `eod_daysheet` (`contactid`, `date`) VALUES ('$contactid', '$today_date')";
    $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);

    unlink("download/eod_".$contactid.".png");

    echo '<script type="text/javascript"> window.location.replace("../home.php"); </script>';
}
?>

<form name="form_sites" method="post" action="" class="form-inline" role="form">

	<div class="panel-group" id="accordion2">

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

		?>

		<?php if($estimate > 0) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_est" >
						<?= ESTIMATE_TILE ?><span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_est" class="panel-collapse collapse">
				<div class="panel-body">
				<?php
				$est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Estimate' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
				echo '<ul>';
				while($est_row = mysqli_fetch_array( $est )) {
					echo '<li>'.$est_row['description'].'</li>';
				}
				echo '</ul>';
				?>
				</div>
			</div>
		</div>
		<?php } ?>

		<?php if($quote > 0) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_quote" >
						Quote<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_quote" class="panel-collapse collapse">
				<div class="panel-body">
				<?php
				$est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Quote' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
				echo '<ul>';
				while($est_row = mysqli_fetch_array( $est )) {
					echo '<li>'.$est_row['description'].'</li>';
				}
				echo '</ul>';
				?>

				</div>
			</div>
		</div>
		<?php } ?>

		<?php if($project > 0) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_project" >
						Project<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_project" class="panel-collapse collapse">
				<div class="panel-body">
				<?php
				$est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Project' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
				echo '<ul>';
				while($est_row = mysqli_fetch_array( $est )) {
					echo '<li>'.$est_row['description'].'</li>';
				}
				echo '</ul>';
				?>

				</div>
			</div>
		</div>
		<?php } ?>

		<?php if($ticket > 0) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click here to view each <?= TICKET_NOUN ?> for today."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ticket" >
						<?= TICKET_NOUN ?><span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_ticket" class="panel-collapse collapse">
				<div class="panel-body">
				<?php
				$est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Ticket' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
				echo '<ul>';
				while($est_row = mysqli_fetch_array( $est )) {
					echo '<li>'.$est_row['description'].'</li>';
				}
				echo '</ul>';
				?>

				</div>
			</div>
		</div>
		<?php } ?>

		<?php if($tasks > 0) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click here to view all Tasks for today."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_task" >
						<?= TASK_TILE ?><span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_task" class="panel-collapse collapse">
				<div class="panel-body">
				<?php
				$est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Task' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
				echo '<ul>';
				while($est_row = mysqli_fetch_array( $est )) {
					echo '<li>'.$est_row['description'].'</li>';
				}
				echo '</ul>';
				?>

				</div>
			</div>
		</div>
		<?php } ?>

		<?php if($communications > 0) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click here to view all Communications for today."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_comm" >
						Communication<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_comm" class="panel-collapse collapse">
				<div class="panel-body">
				<?php
				$est = mysqli_query($dbc, "SELECT * FROM day_overview WHERE type='Communication' AND contactid='$contactid' AND DATE(today_date) = CURDATE()");
				echo '<ul>';
				while($est_row = mysqli_fetch_array( $est )) {
					echo '<li>'.$est_row['description'].'</li>';
				}
				echo '</ul>';
				?>

				</div>
			</div>
		</div>
		<?php } ?>

		<?php
			$query_check_credentials = "SELECT * FROM email_communication_timer WHERE created_by='$contactid' AND DATE(created_date) = CURDATE()";
			$result = mysqli_query($dbc, $query_check_credentials);
			$num_rows = mysqli_num_rows($result);
		?>
		<?php if($num_rows > 0) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click here to view your time tracked in each communication."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_commtimetrack" >
						Communication Time Tracking<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_commtimetrack" class="panel-collapse collapse">
				<div class="panel-body">
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

				</div>
			</div>
		</div>
		<?php } ?>

		<?php
			$query_check_credentials = "SELECT * FROM ticket_timer WHERE created_by='$contactid' AND DATE(created_date) = CURDATE() AND `deleted` = 0";
			$result = mysqli_query($dbc, $query_check_credentials);
			$num_rows = mysqli_num_rows($result);
		?>
		<?php if($num_rows > 0) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click here to view your time tracked in each ticket."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_timetrack" >
						<?= TICKET_NOUN ?> Time Tracking<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_timetrack" class="panel-collapse collapse">
				<div class="panel-body">
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

				</div>
			</div>
		</div>
		<?php } ?>

		<?php
			$query_check_credentials = "SELECT * FROM ticket_comment WHERE created_by='$contactid' AND DATE(created_date) = CURDATE() AND type='day'";
			$result = mysqli_query($dbc, $query_check_credentials);
			$num_rows = mysqli_num_rows($result);
		?>
		<?php if($num_rows > 0) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_daytrack" >
						<?= TICKET_NOUN ?> Day Tracking<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_daytrack" class="panel-collapse collapse">
				<div class="panel-body">
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

				</div>
			</div>
		</div>
		<?php } ?>

		<?php
			$query_check_credentials = "SELECT * FROM tasklist WHERE contactid='$contactid' AND ticketid IS NOT NULL AND (DATE(created_date) = CURDATE() OR DATE(task_tododate) = CURDATE())";
			$result = mysqli_query($dbc, $query_check_credentials);
			$num_rows = mysqli_num_rows($result);
		?>

		<?php if($num_rows > 0) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click here to see your daily Task list."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tickettask" >
						Task List<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_tickettask" class="panel-collapse collapse">
				<div class="panel-body">
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
				</div>
			</div>
		</div>
		<?php } ?>

		<?php
			$query_check_credentials = "SELECT * FROM workorder_timer WHERE created_by='$contactid' AND DATE(created_date) = CURDATE()";
			$result = mysqli_query($dbc, $query_check_credentials);
			$num_rows = mysqli_num_rows($result);
		?>
		<?php if($num_rows > 0) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_wotrack" >
						Work Order Time Tracking<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_wotrack" class="panel-collapse collapse">
				<div class="panel-body">
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

				</div>
			</div>
		</div>
		<?php } ?>

		<?php
			$query_check_credentials = "SELECT * FROM tasklist WHERE contactid='$contactid' AND workorderid IS NOT NULL AND (DATE(created_date) = CURDATE() OR DATE(task_tododate) = CURDATE())";
			$result = mysqli_query($dbc, $query_check_credentials);
			$num_rows = mysqli_num_rows($result);
		?>

		<?php if($num_rows > 0) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_wotask" >
						Work Order Tasklist<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_wotask" class="panel-collapse collapse">
				<div class="panel-body">
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
				</div>
			</div>
		</div>
		<?php } ?>

		<?php
			$query_check_credentials = "SELECT * FROM workorder_comment WHERE created_by='$contactid' AND DATE(created_date) = CURDATE() AND type='day'";
			$result = mysqli_query($dbc, $query_check_credentials);
			$num_rows = mysqli_num_rows($result);
		?>
		<?php if($num_rows > 0) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click here to see your daily Work Order list."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_wodaytrack" >
						Work Order Day Tracking<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_wodaytrack" class="panel-collapse collapse">
				<div class="panel-body">
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

				</div>
			</div>
		</div>
		<?php } ?>
	</div>

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
			<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save your Day Sheet."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		</div>
	</div>

	<div class="clearfix"></div>

</form>
<br>
<div class="panel-group" id="accordion3">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click to view all Day Sheets relevant to you."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a data-toggle="collapse" data-parent="#accordion3" href="#collapse_history" >
					History<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_history" class="panel-collapse collapse">
			<div class="panel-body">
			<?php
				//DATE(NOW())
				//$today = date('Y-m-d');
				$today = '2017-04-10';
				$query_check_credentials = "SELECT * FROM day_overview WHERE today_date = '".$today."' order by today_date DESC";
				$result = mysqli_query($dbc, $query_check_credentials);
				$num_rows = mysqli_num_rows($result);
			?>
			<?php
				echo "<table class='table table-bordered'>
				<tr class='hidden-xs hidden-sm'>
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
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click to view all Day Sheets relevant to you."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a data-toggle="collapse" data-parent="#accordion3" href="#collapse_ds" >
					Daysheet<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_ds" class="panel-collapse collapse">
			<div class="panel-body">
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
					echo '<td data-title="View"><a target="_blank" href="download/Day_'.$row['date'].'-'.$row['contactid'].'.pdf"><img src="'.WEBSITE_URL.'/img/pdf.png" width="'.$img_width.'" height="'.$img_height.'" border="0" alt=""></a></td>';
					echo '</tr>';
				}
				echo '</table>';
				?>

			</div>
		</div>
	</div>
</div>


</div>