<?php error_reporting(0);
include_once('../include.php');
require('../gantti-master/lib/gantti.php');
if(!isset($security)) {
  $security = get_security($dbc, $tile);
  $strict_view = strictview_visible_function($dbc, 'project');
  if($strict_view > 0) {
    $security['edit'] = 0;
    $security['config'] = 0;
  }
}
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project'); ?>
<!-- <h3>Gantt Chart</h3> -->

<div class="notice double-gap-top double-gap-bottom popover-examples">
    <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
    <div class="col-sm-11"><span class="notice-name">NOTE: </span>Here you can see which tickets, checklist items and tasks are scheduled for the project in a daily calendar view. A colored legend displays the status of each ticket, checklist item or task.</div>
    <div class="clearfix"></div>
</div>

<?php $projectid = $_GET['edit'];

    $ticket_status = get_config($dbc, 'ticket_status');
    $each_tab = explode(',', $ticket_status);
    $i=1;
    $status_array = array();
    foreach ($each_tab as $cat_tab) {
            echo '<img src="'.WEBSITE_URL.'/img/block/s'.$i.'.png" width="10" height="10" border="0" alt="">&nbsp;'.$cat_tab.'&nbsp;&nbsp;';
            $status_array[$cat_tab] = 's'.$i.'.png';
            $i++;
    }

echo '<br><span style="float:right;">
            <span class="popover-examples no-gap-pad">
              <a data-toggle="tooltip" data-placement="top" title="Export the Gantt Chart to a .xls Excel file."><img src="../img/info.png" width="20"></a>
            </span>
            <a href="review_project_gantt_chart_excel.php?projectid='.$projectid.'" class="btn brand-btn mobile-block mobile-100">Export to Excel</a></span>';
echo '<br><h3>'.get_project($dbc, $projectid, 'project_name').'</h3>';

$data = array();

$result_ticket = mysqli_query($dbc, "SELECT * FROM tickets WHERE projectid = '$projectid'");
while($row_ticket = mysqli_fetch_array( $result_ticket )) {
    $class = 'current';
    if($row_ticket['status'] == 'Sales/Estimate/RFP') {
        $class = 'class1';
    }
    if($row_ticket['status'] == 'Strategy Needed') {
        $class = 'class2';
    }
    if($row_ticket['status'] == 'Last Minute Priority') {
        $class = 'class3';
    }
    if($row_ticket['status'] == 'Information Gathering') {
        $class = 'class4';
    }
    if($row_ticket['status'] == 'To Be Scheduled') {
        $class = 'class5';
    }
    if($row_ticket['status'] == 'Scheduled/To Do') {
        $class = 'class6';
    }
    if($row_ticket['status'] == 'Doing Today') {
        $class = 'class7';
    }
    if($row_ticket['status'] == 'Done') {
        $class = 'class8';
    }
    if($row_ticket['status'] == 'Internal QA') {
        $class = 'class9';
    }
    if($row_ticket['status'] == 'Customer QA') {
        $class = 'class10';
    }
    if($row_ticket['status'] == 'Waiting On Customer') {
        $class = 'class11';
    }
    if($row_ticket['status'] == 'Done') {
        $class = 'class12';
    }
    if($row_ticket['status'] == 'Sidebar') {
        $class = 'class13';
    }
    if($row_ticket['status'] == 'Archive') {
        $class = 'class14';
    }
    if($row_ticket['to_do_date'] != '0000-00-00' && $row_ticket['to_do_date'] != '') {
        $data[] = array(
          'label' => '<a style="font-size:13px; color:white;" id="'.$row_ticket['ticketid'].'" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row_ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" >#'.$row_ticket['ticketid'].' : '.limit_text($row_ticket['heading'], 5 ).'</a>',
          'start' => $row_ticket['to_do_date'],
          'end'   => date('Y-m-d', strtotime($row_ticket['to_do_end_date'] . ' +1 day')),
          'class' => $class,
        );

        if($row_ticket['internal_qa_date'] != '0000-00-00' && $row_ticket['internal_qa_date'] != '') {
            $data[] = array(
              'label' => '<a style="font-size:13px; color:white;" id="'.$row_ticket['ticketid'].'" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row_ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" >#'.$row_ticket['ticketid'].' : Internal QA</a>',
              'start' => $row_ticket['internal_qa_date'],
              'end'   => $row_ticket['internal_qa_date'],
              'class' => $class,
            );
        }

        if($row_ticket['deliverable_date'] != '0000-00-00' && $row_ticket['deliverable_date'] != '') {
            $data[] = array(
              'label' => '<a style="font-size:13px; color:white;" id="'.$row_ticket['ticketid'].'" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row_ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" >#'.$row_ticket['ticketid'].' : Customer QA</a>',
              'start' => $row_ticket['deliverable_date'],
              'end'   => $row_ticket['deliverable_date'],
              'class' => $class,
            );
        }

    } else {
        $data[] = array(
          'label' => '<a style="font-size:13px; color:white;" id="'.$row_ticket['ticketid'].'" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row_ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" >#'.$row_ticket['ticketid'].' : '.limit_text($row_ticket['heading'], 5 ).'</a>',
          'start' => date('Y-m-d'),
          'end'   => date('Y-m-d', time()+86400),
          'class' => $class,
        );

        if($row_ticket['internal_qa_date'] != '0000-00-00' && $row_ticket['internal_qa_date'] != '') {
            $data[] = array(
              'label' => '<a style="font-size:13px; color:white;" id="'.$row_ticket['ticketid'].'" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row_ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" >#'.$row_ticket['ticketid'].' : Internal QA</a>',
              'start' => $row_ticket['internal_qa_date'],
              'end'   => $row_ticket['internal_qa_date'],
              'class' => $class,
            );
        }

        if($row_ticket['deliverable_date'] != '0000-00-00' && $row_ticket['deliverable_date'] != '') {
            $data[] = array(
              'label' => '<a style="font-size:13px; color:white;" id="'.$row_ticket['ticketid'].'" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row_ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" >#'.$row_ticket['ticketid'].' : Customer QA</a>',
              'start' => $row_ticket['deliverable_date'],
              'end'   => $row_ticket['deliverable_date'],
              'class' => $class,
            );
        }
    }
}

$gantti = new Gantti($data, array(
    'title'      => '#'.$projectid,
  'cellwidth'  => 30,
  'cellheight' => 30,
  'today'      => true
));
echo $gantti; ?>
<?php include('next_buttons.php'); ?>