<?php
if(!empty($_GET['category'])) {
    echo '<button type="submit" name="printpdf" id="printpdf" value="Print Report" class="btn brand-btn pull-right">Print</button>';
}

echo '<input type="hidden" name="ticket_category" id="ticket_category" value="'.$_GET['category'].'" />';

if(vuaed_visible_function($dbc, 'ticket') == 1) {
    echo '<div class="pull-right"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Add a ticket for this project."><img src="../img/info.png" width="20"></a></span>';
		echo '<a id="'.$projectid.'" href="'.WEBSITE_URL.'/Ticket/add_tickets.php?projectid='.$projectid.'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" class="btn brand-btn mobile-block">Add Ticket</a></div>';
}

$internal_active_tab = '';
$external_active_tab = '';
if($_GET['category'] == 'Active') {
    $internal_active_tab = 'active_tab';
}
if($_GET['category'] == 'Archive') {
    $external_active_tab = 'active_tab';
}

echo '<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="These are the tickets that are still active and being worked on for the project."><img src="../img/info.png" width="20"></a></span>';
echo "<a href='review_project.php?type=ticket&projectid=".$projectid."&category=Active&from_url=".urlencode($_GET['from_url'])."'><button type='button' class='btn brand-btn mobile-block ".$internal_active_tab."'>Active</button></a>&nbsp;&nbsp;</div>";
echo '<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="These are the tickets that have been completed and archived for the project."><img src="../img/info.png" width="20"></a></span>';
echo "<a href='review_project.php?type=ticket&projectid=".$projectid."&category=Archive&from_url=".urlencode($_GET['from_url'])."'><button type='button' class='btn brand-btn mobile-block ".$external_active_tab."'>Archive</button></a>&nbsp;&nbsp;</div>";
echo "<br /><br />";

$rowsPerPage = 25;
$pageNum = 1;

if(isset($_GET['page'])) {
    $pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

if($_GET['category'] == 'Archive') {
    $query_check_credentials = "SELECT t.*, c.name FROM tickets t, contacts c WHERE t.businessid = c.contactid AND t.status = 'Archive' AND projectid='$projectid' ORDER BY ticketid DESC LIMIT $offset, $rowsPerPage";
    $query = "SELECT count(t.ticketid) as numrows FROM tickets t, contacts c WHERE t.businessid = c.contactid AND t.status = 'Archive' AND projectid='$projectid'";
} else {
    $query_check_credentials = "SELECT t.*, c.name FROM tickets t, contacts c WHERE t.businessid = c.contactid AND t.status != 'Archive' AND projectid='$projectid' ORDER BY ticketid DESC LIMIT $offset, $rowsPerPage";
    $query = "SELECT count(t.ticketid) as numrows FROM tickets t, contacts c WHERE t.businessid = c.contactid AND t.status != 'Archive' AND projectid='$projectid'";
}
$result = mysqli_query($dbc, $query_check_credentials);
$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {
    // Added Pagination //
    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
    // Pagination Finish //

    echo '<div id="no-more-tables"><table class="table table-bordered">';
    echo '<tr class="hidden-xs hidden-sm">
        <th>Ticket#</th>
        <th>Service</th>
        <th>Ticket Heading</th>
        <th>TO DO</th>
        <th>Internal QA</th>
        <th>Deliverable</th>
        <th>Current Status</th>
        <th>Function</th>
        </tr>';
} else {
    echo "<h2>No Record Found.</h2>";
}
while($row = mysqli_fetch_array( $result )) {
    echo '<tr>';
    $clientid = $row['clientid'];
    $contactid = $row['contactid'];
    $ticketid = $row['ticketid'];

    if(vuaed_visible_function($dbc, 'ticket') == 1) {
        echo '<td><a href="'.WEBSITE_URL.'/Ticket/add_tickets.php?ticketid='.$ticketid.'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" id="'.$ticketid.'">'.$ticketid. '</a></td>';
    } else {
        echo '<td data-title="Ticket#">' . $ticketid . '</td>';
    }

    echo '<td data-title="Service">' . $row['service_type'].'<br>'.$row['service'] .'<br>'.$row['sub_heading'] . '</td>';
    echo '<td data-title="Ticket Heading">' . $row['heading'] . '</td>';

    $to = explode(',', $row['contactid']);
    $staff = '';
    foreach($to as $category => $value)  {
        if($value != '') {
            $staff .= get_staff($dbc, $value).'<br>';
        }
    }

    $iqa = explode(',', $row['internal_qa_contactid']);
    $internal_qa = '';
    foreach($iqa as $category => $value)  {
        if($value != '') {
            $internal_qa .= get_staff($dbc, $value).'<br>';
        }
    }

    $del = explode(',', $row['deliverable_contactid']);
    $deliverable = '';
    foreach($del as $category => $value)  {
        if($value != '') {
            $deliverable .= get_staff($dbc, $value).'<br>';
        }
    }

    //echo '<td data-title="TO DO">' . $row['to_do_date'].'<br>'.$staff.$row['max_time'].'</td>';
    echo '<td data-title="TO DO">' . $row['to_do_date'].' : '.$row['to_do_end_date'].'<br>'.$staff.$row['max_time'].'</td>';
    echo '<td data-title="Internal QA">' . $row['internal_qa_date'].'<br>'.$internal_qa. '</td>';
    echo '<td data-title="Deliverable">' . $row['deliverable_date'].'<br>'.$deliverable. '</td>';

    echo '<td data-title="Current Status">' . $row['status'] . '</td>';
    echo '<td data-title="Function">';
    if(vuaed_visible_function($dbc, 'ticket') == 1) {
		 echo '<a href="'.WEBSITE_URL.'/Ticket/add_tickets.php?ticketid='.$row['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" id="'.$row['ticketid'].'">View/Edit</a>';
    }
    echo '</td>';

    echo "</tr>";
}

echo '</table></div>';
// Added Pagination //
echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
// Pagination Finish //
